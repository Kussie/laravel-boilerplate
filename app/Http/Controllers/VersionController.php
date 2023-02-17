<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasApiResponses;
use App\Http\Resources\VersionResource;
use App\Models\Version;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Throwable;

class VersionController extends Controller
{
    use HasApiResponses;

    public function index(Request $request, string $type): JsonResponse
    {
        $perPage = ($request->has('per_page') ? $request->per_page : config('features.auth.per_page_default'));
        $versions = Version::approved()->where('type', $type)->orderBy('version', 'desc')->paginate($perPage);

        if (! $versions->count()) {
            return $this->respondNotFound('There is no Versions');
        }

        return $this->respondSuccessWithData(VersionResource::collection($versions));
    }

    public function show(Request $request, string $type, string $version): JsonResponse
    {
        try {
            $version = Version::approved()
                ->where('type', $type)
                ->orWhere(function ($query) use ($version): void {
                    $query->where('version', $version)
                        ->Orwhere('uuid', $version);
                })
                ->with(['package'])
                ->firstOrFail();

            return $this->respondSuccessWithData(new VersionResource($version));
        } catch (Throwable $ex) {
            return $this->handleError($ex);
        }
    }
}
