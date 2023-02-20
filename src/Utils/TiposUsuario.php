<?php

namespace App\Utils;

enum TiposUsuario: string {
    case ROOT = "Root";
    case ADMIN = "Administrador";
    case SECRETARIA = "Secretaria";
    case PROFESOR = "Profesor";
    case ALUMNO = "Alumno";
}