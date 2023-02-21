<?php

namespace App\Command;

use App\Entity\Ruta;
use App\Entity\Usuario;
use App\Repository\RutaRepository;
use App\Repository\UsuarioRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: "app:install", description: "Instala las deps. iniciales", hidden:false, aliases: ["app:first-conf"])]
class Install extends Command
{
    public function __construct(
        private UsuarioRepository $ur,
        private EntityManagerInterface $em,
        private RutaRepository $rrepo,
        private UserPasswordHasherInterface $hasher
    ) {
        parent::__construct();
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper("question");
        $output->writeln("Verificando usuario root");
        $root = $this->ur->findOneBy(["username" => "root"]);
        if (!$root) {
            $this->createRoot($input, $output, $helper);
        } else {
            $output->writeln("<info>Usuario root encontrado ({$root->getCorreo()})</info>");
        }

        $output->writeln("Creando rutas");
        foreach ($this->getRutas() as $ruta) {
            if ($this->rrepo->findOneBy(["id" => $ruta["id"]])) continue;
            $r = new Ruta();
            $r->setId($ruta["id"])
                ->setPath($ruta["path"])
                ->setLabel($ruta["label"])
                ->setName($ruta["name"])
                ->setIcon($ruta["icon"])
                ->setMinimumRole($ruta["minimumRole"])
                ->setParent($ruta["parent"] ? $this->rrepo->find($ruta["parent"]) : null)
            ;
            $this->rrepo->save($r, true);
        }

        return Command::SUCCESS;
    }
    private function createRoot(InputInterface $input, OutputInterface $output, QuestionHelper $helper)
    {
        $root = new Usuario();
        $output->writeln("Creando al usuario root");
        $question = new Question("Introduzca la contraseña para root: ");
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $root->plainPassword = $helper->ask($input, $output, $question);
        if (!$root->plainPassword) {
            $output->writeln("<error>Debe introducir una contraseña para root</error>");
            return Command::FAILURE;
        }
        $question = new Question("Repita la contraseña: ");
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $repeat = $helper->ask($input, $output, $question);
        if ($repeat != $root->plainPassword) {
            $output->writeln("<error>Las contraseñas no coinciden</error>");
            return Command::FAILURE;
        }
        while (!$root->getCorreo()) {
            $question = new Question("introduzca un correo para el usuario root: ");
            $question->setHidden(false);
            $root->setCorreo($helper->ask($input, $output, $question));
        }
        $root
            ->setUsername("root")
            ->setNombres("Root")
            ->setApellidoPaterno("Del")
            ->setApellidoMaterno("Sistema")
            ->setCreatedAt(new DateTimeImmutable())
            ->setPassword($this->hasher->hashPassword($root, $root->plainPassword))
            ->setRoles([
                "ROLE_ROOT"
            ])
        ;
        $root->eraseCredentials();

        $this->em->persist($root);
        $this->em->flush();
        $output->writeln("<info>Usuario root creado!</info>");
    }

    public function getRutas(): array 
    {
        return [
            [
                "id" => 1,
                "path" => "/",
                "label" => "Inicio",
                "name" => "app_home",
                "icon" => "home",
                "minimumRole" => "ROLE_USER",
                "parent" => null
            ],
            [
                "id" => 2,
                "path" => "/admin/usuarios",
                "label" => "Usuarios",
                "name" => "admin_users",
                "icon" => "group",
                "minimumRole" => "ROLE_SECRETARIA",
                "parent" => null
            ],
            [
                "id" => 3,
                "path" => "/logout",
                "label" => "Cerrar sesión",
                "name" => "app_logout",
                "icon" => "logout",
                "minimumRole" => "ROLE_USER",
                "parent" => null
            ]
        ];
    }
}
