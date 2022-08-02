<?php

namespace App\Domain\Models;

enum AccountType: string
{
    case Reseller = 'reseller';
    case Distributor = 'distributor';
    case Customer = 'customer';
}
