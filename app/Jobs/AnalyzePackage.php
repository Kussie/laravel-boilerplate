<?php

namespace App\Jobs;

use App\Events\PackageProcessingFailed;
use App\Events\PackageProcessingStarted;
use App\Exceptions\ImportFileCorruptException;
use App\Exceptions\ImportFileCouldNotBeExtractedException;
use App\Exceptions\ImportFileCouldNotBeRetrivedException;
use App\Exceptions\ImportFileEmptyException;
use App\Exceptions\ImportFileHashMismatchException;
use App\Exceptions\MalformedContentException;
use App\Exceptions\ManifestMismatchException;
use App\Exceptions\NotImplementedException;
use App\Exceptions\ReferencedFileNotFoundException;
use App\Exceptions\UnexpectedRuleSetException;
use App\Models\Package;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\MaxAttemptsExceededException;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

use function get_class;
use function in_array;

class AnalyzePackage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 7200;

    protected string $packageDisk;

    /**
     * Create a new job instance.
     */
    public function __construct(public Package $package)
    {
    }

    public function handle(): void
    {
        Log::info('package', ['package' => $this->package]);

        if ($this->package->location === 's3') {
            // download package
            $filePath = $this->package->filename;

            if (! empty($this->package->path)) {
                $filePath = $this->package->path . '/' . $this->package->filename;
            }

            if (! Storage::disk('s3packages')->exists($filePath)) {
                Log::error('Package does not exist on S3', ['package' => $this->package]);
                event(new PackageProcessingFailed($this->package, [
                    'reason' => 'Package does not exist on S3',
                    'expected' => $this->package->filename,
                ]));

                throw new ReferencedFileNotFoundException('Package does not exist on S3');
            }
            Storage::disk('packages')->put(
                $this->package->filename,
                Storage::disk('s3packages')->get($filePath)
            );
        }

        // check hash
        if (hash_file('sha1', Storage::disk('packages')->path($this->package->filename)) !== $this->package->hash) {
            Log::error('Hash Mismatch Error', ['package' => $this->package]);
            event(new PackageProcessingFailed($this->package, [
                'reason' => 'Hash mismatch',
                'expected' => $this->package->hash,
                'received' => hash_file('sha1', Storage::disk('packages')->path($this->package->filename)),
            ]));

            throw new ImportFileHashMismatchException();
        }

        Storage::disk('packages')->makeDirectory($this->package->hash);
        $this->packageDisk = "package::{$this->package->hash}";
        config(['filesystems.disk' . $this->packageDisk => [
            'driver' => 'local',
            'root' => storage_path('app/packages/' . $this->package->hash),
        ]]);

        $filePath = Storage::disk('packages')->path($this->package->filename);

        // Check ZIP size and amount of files
        $size = Storage::disk('packages')->size($this->package->filename);
        $zipFile = new \PhpZip\ZipFile();
        $count = $zipFile->openFile($filePath)->count();

        // Check files in archive
        if ($count === 0 || $size === 0) {
            Log::error('Archive is empty', ['package' => $this->package]);
            event(new PackageProcessingFailed($this->package, [
                'reason' => 'Archive is empty',
            ]));

            throw new ImportFileEmptyException();
        }

        // extract package
        try {
            $zipFile
                ->openFile($filePath)
                ->extractTo(Storage::disk($this->packageDisk)->path(''));
        } catch (Exception $e) {
            Log::error('Archive could not be extracted', ['package' => $this->package, 'error' => $e->getMessage()]);
            event(new PackageProcessingFailed($this->package, [
                'reason' => 'Archive could not be extracted',
                'error' => $e->getMessage(),
            ]));

            throw new ImportFileCouldNotBeExtractedException();
        }

        try {
            // verify contents with manifest file
            $manifest = simplexml_load_string(Storage::disk($this->packageDisk)->get('manifest.xml'));

            foreach ($manifest->folder as $folder) {
                $folderName = (string) $folder['name'];

                foreach ($folder->file as $file) {
                    $manifestFile = (string) $file;

                    if (! Storage::disk($this->packageDisk)->has($folderName . '/' . $manifestFile)) {
                        Log::error('File in Manifest doesnt exist', ['package' => $this->package, 'file' => Storage::disk($this->packageDisk)->path($folderName . '/' . $manifestFile)]);
                        event(new PackageProcessingFailed($this->package, [
                            'reason' => 'File in Manifest doesnt exist',
                            'file' => Storage::disk($this->packageDisk)->path($folderName . '/' . $manifestFile),
                        ]));

                        throw new ReferencedFileNotFoundException();
                    }
                }
            }
        } catch (Exception $e) {
            Log::error('Could not load manifest.xml', ['package' => $this->package, 'error' => $e->getMessage()]);
            event(new PackageProcessingFailed($this->package, [
                'reason' => 'Could not load manifest.xml',
                'error' => $e->getMessage(),
            ]));

            throw new ImportFileCouldNotBeExtractedException();
        }

        // Get some details from the manifest
        //$ruleSet = strtoupper((string) $manifest->ruleset);
        $this->package->publisher_name = (string) $manifest->username;
        $this->package->publisher_email = (string) $manifest->useremail;
        // figure out type and determine parser to use
        //$this->package->parser_class = '';
        //$this->package->parser_version = '';
        $this->package->save();
        $this->package->refresh();

        // trigger processing start event
        event(new PackageProcessingStarted($this->package));  // todo move to processing job

        // process rules
        // trigger actual parsing job
    }

    public function failed(Throwable $exception): void
    {
        $errors = [
            ImportFileCouldNotBeExtractedException::class,
            ImportFileCorruptException::class,
            ImportFileCouldNotBeRetrivedException::class,
            ImportFileEmptyException::class,
            ImportFileHashMismatchException::class,
            ManifestMismatchException::class,
            ReferencedFileNotFoundException::class,
            UnexpectedRuleSetException::class,
            MalformedContentException::class,
            NotImplementedException::class,
            MaxAttemptsExceededException::class,
        ];

        if (in_array(get_class($exception), $errors)) {
            if (get_class($exception) === MaxAttemptsExceededException::class) {
                $errorMessage = 'The system timed out processing the package';
            } else {
                $errorMessage = $exception->getMessage();
            }
        } else {
            $errorMessage = 'A system error occurred whilst attempting to parse package';
        }
        Log::error($errorMessage, ['package' => $this->package, 'reason' => $errorMessage, 'error' => $exception->getMessage(), 'exception' => get_class($exception)]);
        event(new PackageProcessingFailed($this->package, [
            'reason' => $errorMessage,
            'error' => $exception->getMessage(),
            'exception' => get_class($exception),
        ]));
    }
}
