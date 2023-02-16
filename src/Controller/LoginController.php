<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
