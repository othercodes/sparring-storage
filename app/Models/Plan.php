<?php

namespace App\Models;

use App\Traits\WithUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory, WithUuid;

    protected $keyType = 'uuid';
    public $incrementing = false;

    protected $primaryKey = 'sku';
}
