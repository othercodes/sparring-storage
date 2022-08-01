<?php

namespace App\Models;


use Laravel\Passport\Token;

class PassportToken extends Token
{
    public static function boot()
    {
        parent::boot();

        static::created(function (PassportToken $model) {
            $model->user_id = $model->client()->first()->user_id;
            $model->save();
        });
    }
}
