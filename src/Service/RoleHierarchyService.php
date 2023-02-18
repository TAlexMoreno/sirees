<?php

namespace App\Service;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class RoleHierarchyService 
{
    public function __construct(private Security $security)
    {
        return;
    }
    public function getRoles(): array
    {
        $roles = [
            "Administrador" => "ROLE_ADMIN",
            "Secretaria" => "ROLE_SECRETARIA",
            "Profesor" => "ROLE_PROFESOR",
            "Alumno" => "ROLE_ALUMNO"
        ];
        if (!$this->security->isGranted("ROLE_ROOT")) {
            unset($roles["Administrador"]);
        }
        if (!$this->security->isGranted("ROLE_ADMIN")){
            unset($roles["Secretaria"]);
        }
        if (!$this->security->isGranted("ROLE_SECRETARIA")) return [];
        return $roles;
    }
}
