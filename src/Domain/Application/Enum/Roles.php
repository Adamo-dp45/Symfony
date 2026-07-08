<?php

namespace App\Domain\Application\Enum;

enum Roles: string {
    case ADMIN = 'ROLE_ADMIN';
    case USER = 'ROLE_USER';
}