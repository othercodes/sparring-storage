<?php

namespace App\Infrastructure\Laravel\Models;

use App\Infrastructure\Laravel\Traits\WithUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Subscription extends Model
{
    use HasFactory, WithUuid;

    protected $keyType = 'uuid';
    public $incrementing = false;

    /**
     * Properties:
     * - account_id
     * - plan_sku
     * - quantity
     * - status
     * - subscribed_at
     */

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    /**
     * A Subscription
     * @return BelongsTo
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_sku', 'sku');
    }
}
