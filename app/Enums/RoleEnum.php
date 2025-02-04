<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case SELLER = 'seller';
    case USER = 'user';
}
