<?php

namespace App\Infrastructure\Laravel\Models;

use App\Infrastructure\Laravel\Traits\WithUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\HasApiTokens;

/**
 * @mixin Builder
 */
class Account extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, WithUuid;
    protected $keyType = 'uuid';
    public $incrementing = false;
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function isReseller(): bool
    {
        return $this->type == 'reseller';
    }

    public function isDistributor(): bool
    {
        return $this->type == 'distributor';
    }

    public function canHaveAPICredentials(): bool
    {
        return $this->isDistributor() || $this->isReseller();
    }

    protected static function boot() {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });

        static::created(function (Account $model) {
            if ($model->canHaveAPICredentials()) {
                $clients = App::make(ClientRepository::class);

                // 1st param is the user_id - none for client credentials
                // 2nd param is the client name
                // 3rd param is the redirect URI - none for client credentials
                $client = $clients->create($model->id, $model->name, '');
            }
        });
    }
}
