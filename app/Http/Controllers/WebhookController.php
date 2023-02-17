<?php

namespace App\Http\Controllers;

use App\Enums\PackageStatus;
use App\Events\WebhookTriggered;
use App\Http\Controllers\Traits\HasApiResponses;
use App\Http\Requests\WebhookRequest;
use App\Http\Resources\PackageResource;
use App\Jobs\AnalyzePackage;
use App\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Throwable;

class WebhookController extends Controller
{
    use HasApiResponses;

    public function add(WebhookRequest $request): JsonResponse
    {
        Log::debug('Webhook triggered', $request->all());

        if (config('queue.default') === 'sync') {
            return $this->respondError('Webhook processing is disabled when queue driver is set to sync', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // process incoming webhook and trigger job
        $location = $request->input('packageHost');
        $path = $request->input('packagePath', null);

        $package = new Package();
        $package->hash = $request->input('packageHash');
        $package->filename = $request->input('packageName');
        $package->status = PackageStatus::QUEUED;
        $package->location = $location;
        $package->path = $path;
        $package->request_ip = $request->ip();
        $package->save();

        try {
            AnalyzePackage::dispatch($package);
        } catch (Throwable $e) {
            return $this->handleError($e);
        }

        event(new WebhookTriggered($package));

        return $this->respondSuccessWithData(new PackageResource($package));
    }

    public function log(): JsonResponse
    {
        $packages = Package::orderBy('created_at', 'desc')->limit(10)->get();

        return $this->respondSuccessWithData(PackageResource::collection($packages));
    }

    public function status(Request $request, Package $package): JsonResponse
    {
        return $this->respondSuccessWithData(new PackageResource($package));
    }
}
