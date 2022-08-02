<?php

namespace App\Domain\Models;
use App\Infrastructure\Laravel\Traits\WithUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory, WithUuid;

    protected $keyType = 'uuid';
    public $incrementing = false;

    protected $primaryKey = 'sku';

    protected $fillable = [
        'name',
        'sku',
        'maximum',
    ];
}
