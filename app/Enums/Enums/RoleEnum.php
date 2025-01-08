<?php

namespace App\Enums\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case KASIR = 'kasir';
}
