<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait WithUuid
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot() {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }
}
