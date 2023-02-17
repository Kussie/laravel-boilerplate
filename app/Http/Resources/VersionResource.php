<?php

namespace App\Http\Resources;

use App\Models\Version;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Version
 */
class VersionResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Version $this */
        return array_merge(
            parent::toArray($request),
            [
                'meta' => [
                    'links' => [
                        'self' => route('section.versions.show', ['version' => $this->uuid, 'type' => $this->type]),
                    ],
                ],
            ]
        );
    }
}
