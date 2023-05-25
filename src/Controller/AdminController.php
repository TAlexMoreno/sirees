<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\Type\AlumnoType;
use App\Repository\UsuarioRepository;
use App\Service\RoleHierarchyService;
use App\Utils\RolesUsuarios;
use App\Utils\TiposUsuario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        $this->denyAccessUnlessGranted(RolesUsuarios::SECRETARIA->value);
        return $this->render("admin/usuarios.html.twig");
    }

    #[Route("/admin/usuarios/{username}", name:"admin_usuario_show")]
    public function usuarioShow(String $username, UsuarioRepository $urepo)
    {
        $this->denyAccessUnlessGranted(RolesUsuarios::SECRETARIA->value);
        $user = $urepo->findOneBy(["username" => $username]);
        if (!$user) {
            throw $this->createNotFoundException("Usuario no encontrado");
        }
        /** @var Usuario $lUser */
        $lUser = $this->getUser();
        $form = $this->createForm($user->getFormType(), $user);
        return $this->render("admin/usuario.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route("/admin/usuarios/nuevo/{type}", name:"admin_usuarios_new")]
    public function usuarioNew(string $type, UsuarioRepository $urepo)
    {
        $this->denyAccessUnlessGranted(RolesUsuarios::SECRETARIA->value);
        $form = $this->createForm("App\\Form\\Type\\".ucfirst($type)."Type", null, [
            "matriculaProvisional" => $urepo->getNewMatricula(TiposUsuario::from(ucfirst($type)))
        ]);
        return $this->render("admin/usuarios_nuevo.html.twig", [
            "form" => $form->createView(),
            "type" => ucfirst($type) 
        ]);
    }

    #[Route("/ajaxUtils/roles", name: "app_get_roles")]
    public function getRoles(RoleHierarchyService $roles){
        return $this->json($roles->getRoles());
    }
}
