<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route("/", "app_home")]
    public function appHome()
    {
        $this->denyAccessUnlessGranted("ROLE_USER");
        return $this->render("home/index.html.twig");
    }
}
