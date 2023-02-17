<?php

namespace App\Controller;

use App\Repository\UsuarioRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route("/login", name:"app_login")]
    public function index(AuthenticationUtils $utils)
    {
        return $this->render("login/index.html.twig", [
            "last_username" => $utils->getLastUsername(),
            "error" => $utils->getLastAuthenticationError()
        ]);
    }

    #[Route("logout", name:"app_logout")]
    public function logout()
    {
        return new Response("El authcontroller lo maneja");
    }
}
