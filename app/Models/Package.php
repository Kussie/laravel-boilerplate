<?php

namespace App\Models;

use App\Enums\PackageStatus;
use App\Models\Traits\InteractsWithUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property Collection|mixed $collection
 * @property \Illuminate\Support\Carbon $created_at
 * @property string $filename
 * @property string $finished_processing
 * @property string $hash
 * @property string $location
 * @property string $meta
 * @property string $parser_class
 * @property string $parser_version
 * @property string $path
 * @property string $publisher_email
 * @property string $publisher_name
 * @property string $request_ip
 * @property string $started_processing
 * @property mixed $status
 */
class Package extends Model
{
    use InteractsWithUuid;

    protected $primaryKey = 'uuid';

    protected $hidden = [
        'publisher_name',
        'publisher_email',
        'meta',
    ];

    protected $appends = [
        'status_text',
    ];

    protected $casts = [
        'meta' => 'json',
        'started_processing' => 'datetime',
        'finished_processing' => 'datetime',
        'status' => PackageStatus::class,
    ];

    public function versions(): HasMany
    {
        return $this->hasMany(Version::class, 'package_hash', 'hash');
    }

    protected function statusText(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->status->description(),
        );
    }
}
