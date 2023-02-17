<?php

namespace App\Models;

use App\Models\Traits\InteractsWithUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

/**
 * @property Collection|mixed $collection
 * @property \Illuminate\Support\Carbon $created_at
 * @property string $type
 */
class Version extends Model
{
    use HasFactory;
    use InteractsWithUuid;

    protected $primaryKey = 'uuid';

    protected $hidden = [];

    protected $casts = [
        'files' => 'array',
    ];

    protected $dates = ['start_date', 'end_date'];

    public function package(): HasOne
    {
        return $this->hasOne(Package::class, 'hash', 'package_hash');
    }

    public function scopeByType($query, string $type): void
    {
        $query->where('type', $type);
    }

    public function scopeApproved($query): void
    {
        $query->where('approved', 1);
    }

    public function scopeVisible($query): void
    {
        $query->where('visible', 1);
    }

    public function scopeHidden($query): void
    {
        $query->where('visible', 0);
    }

    public function scopeArchived($query): void
    {
        $query->where('archived', 1);
    }

    public function scopeParsed($query): void
    {
        $query->where('parsed', 1);
    }
}
