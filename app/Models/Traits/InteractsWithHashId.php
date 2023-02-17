<?php

declare(strict_types=1);

namespace App\Models\Traits;

use Leuverink\HashidBinding\HashidService;

trait InteractsWithHashId
{
    public function initializeHashidBinding(): void
    {
        $this->append('encoded_route_key');
    }

    /**
     * Get the encoded value of the model's route key.
     */
    public function getRouteKey(): mixed
    {
        return $this->encodedRouteKey;
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param null $field
     */
    public function resolveRouteBinding($hashid, $field = null): ?\Illuminate\Database\Eloquent\Model
    {
        $decodedKey = resolve(HashidService::class)->decode($hashid, __CLASS__);

        return $this->where($this->getRouteKeyName(), $decodedKey)->first();
    }

    public function getEncodedRouteKeyAttribute(): string
    {
        $routeKey = parent::getRouteKey();

        return resolve(HashidService::class)->encode($routeKey, __CLASS__);
    }
}
