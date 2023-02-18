<?php

namespace App\Controller;

use App\Service\RoleHierarchyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    public function __construct()
    {
        return;
    }

    #[Route("/admin/usuarios", name: "admin_usuarios")]
    public function usuarios()
    {
        return $this->render("admin/usuarios.html.twig");
    }
    #[Route("/admin/usuarios/nuevo/{type}", name:"admin_usuarios_new")]
    public function usuarioNew(string $type)
    {
        $form = $this->createForm("App\\Form\\Type\\".ucfirst($type)."Type");
        return $this->render("admin/usuarios_nuevo.html.twig", [
            "form" => $form->createView(),
            "type" => ucfirst($type)
        ]);
    }
}
