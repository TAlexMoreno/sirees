<?php

namespace App\Utils;

enum TiposUsuario: string {
    case ROOT = "Root";
    case ADMIN = "Administrador";
    case SECRETARIA = "Secretaria";
    case PROFESOR = "Profesor";
    case ALUMNO = "Alumno";
}

enum RolesUsuarios: string {
    case ROOT = "ROLE_ROOT";
    case ADMIN = "ROLE_ADMIN";
    case SECRETARIA = "ROLE_SECRETARIA";
    case PROFESOR = "ROLE_PROFESOR";
    case ALUMNO = "ROLE_ALUMNO";
}