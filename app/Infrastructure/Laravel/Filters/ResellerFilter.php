<?php

namespace App\Infrastructure\Laravel\Filters;

class ResellerFilter extends QueryFilter
{
    public function id(string $id): void
    {
        $this->builder->where('id', '=', $id);
    }

    public function name(string $name): void
    {
        $this->builder->where('name', '=', $name);
    }

    public function email(string $email): void
    {
        $this->builder->where('email', '=', $email);
    }
}
