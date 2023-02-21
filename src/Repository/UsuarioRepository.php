<?php

namespace App\Repository;

use App\Entity\Usuario;
use App\Utils\RolesUsuarios;
use App\Utils\TiposUsuario;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Usuario>
 *
 * @method Usuario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Usuario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Usuario[]    findAll()
 * @method Usuario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usuario::class);
    }

    public function save(Usuario $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Usuario $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Usuario) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

    public function getNewMatricula($type=TiposUsuario::ALUMNO): string
    {
        $role = array_filter(RolesUsuarios::cases(), function(RolesUsuarios $rol)use($type){
            return $type->name == $rol->name;
        });
        $role = array_values($role)[0];
        $qb = $this->createQueryBuilder("u")
            ->where("u.roles like :role")
            ->andWhere("u.createdAt > :thisYear")
            ->setParameters([
                "role" => "%{$role->value}%",
                "thisYear" => $this->getFirstMomentOfYear()
            ])
            ->orderBy("u.username", "DESC")
            ->setMaxResults(1)
        ;
        /** @var ?Usuario $last */
        $last = $qb->getQuery()->getOneOrNullResult();
        $now = new DateTime();
        $next = !$last ? 1 : intval(substr($last->getUsername(), -6, 6)) + 1;
        return $this->getFormatoMatricula($next, $now, $type);
    }
    private function getFormatoMatricula(int $next, DateTime $now, TiposUsuario $type){
        switch($type){
            case TiposUsuario::ALUMNO: return $now->format("Y").str_pad($next, 6, "0", STR_PAD_LEFT); 
            case TiposUsuario::ADMIN: return "AR".str_pad($next, 4, "0", STR_PAD_LEFT);
            case TiposUsuario::SECRETARIA: return "SA".str_pad($next, 4, "0", STR_PAD_LEFT);
            case TiposUsuario::PROFESOR: return "PR".str_pad($next, 4, "0", STR_PAD_LEFT);
        }
        
    }
    public function getFirstMomentOfYear(): DateTime {
        $now = new DateTime();
        $firstMomento = new DateTime("{$now->format("Y")}-01-01 00:00:00");
        return $firstMomento;
    }

    private function getRole()
    {

    }
}
