<?php

namespace App\Http\Resources;

use App\Models\Package;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Package
 */
class PackageResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Package $this */
        return array_merge(
            parent::toArray($request),
            [
                'meta' => [
                    'links' => [
                        'status' => route('webhooks.packages.status', [$this->uuid]),
                    ],
                ],
            ]
        );
    }
}
