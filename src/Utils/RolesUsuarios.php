<?php

namespace App\Utils;

enum RolesUsuarios: string {
    case ROOT = "ROLE_ROOT";
    case ADMIN = "ROLE_ADMIN";
    case SECRETARIA = "ROLE_SECRETARIA";
    case PROFESOR = "ROLE_PROFESOR";
    case ALUMNO = "ROLE_ALUMNO";
}