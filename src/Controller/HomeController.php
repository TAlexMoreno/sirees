<?php

namespace App\Controller;

use App\Entity\Ruta;
use App\Repository\RutaRepository;
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

    #[Route("/ajaxUtils/rutas", "app_get_rutas")]
    public function getRutas(RutaRepository $rrepo){
        $rutas = $rrepo->findAll();
        $rutas = array_filter($rutas, function(Ruta $ruta){
            return $this->isGranted($ruta->getMinimumRole());
        });
        return $this->json(array_values($rutas));
    }
}
