<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProgramaController extends AbstractController {
    public function __construct()
    {
        return;
    }

    #[Route("/programas", name: "app_programas")]
    public function programas(){
        return $this->render("programa/programas.html.twig");
    }
}