<?php

namespace App\Infrastructure\Laravel\Models;

use App\Infrastructure\Laravel\Traits\WithUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory, WithUuid;

    protected $keyType = 'uuid';
    public $incrementing = false;

    protected $primaryKey = 'sku';
}
