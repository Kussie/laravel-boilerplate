<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property Collection|mixed $collection
 * @property string $created_at
 * @property string $email
 * @property string $email_verified_at
 * @property string $first_name
 * @property string $full_name
 * @property string $last_name
 * @property string $password
 * @property bool $processing_notifications
 * @property string $remember_token
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use HasUuids;
    use Notifiable;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'full_name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty();
    }

    /**
     * The attributes that are logged.
     *
     * @var array
     */
    protected static $logAttributes = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    public function FullName(): Attribute
    {
        return new Attribute(
            get: fn () => "{$this->first_name} {$this->last_name}",
            set: function ($value): array {
                $nameParts = explode(' ', $value);

                return [
                    'first_name' => $nameParts[0],
                    'last_name' => $nameParts[1],
                ];
            }
        );
    }
}
