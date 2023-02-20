<?php

namespace App\Tests\Integration;

use App\Repository\UsuarioRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase {
    public function testGetFirstMomento(){
        self::bootKernel();
        $container = static::getContainer();
        /** @var UsuarioRepository $userRepo */
        $userRepo = $container->get(UsuarioRepository::class);
        $this->assertEquals(new DateTime("2023-01-01 00:00:00"), $userRepo->getFirstMomentOfYear(), "FirstMomento");
    }
    public function testGetMatricula(){
        self::bootKernel();
        $container = static::getContainer();
        /** @var UsuarioRepository $userRepo */
        $userRepo = $container->get(UsuarioRepository::class);
        $this->assertEquals("2023000001", $userRepo->getNewMatricula());
    }
}