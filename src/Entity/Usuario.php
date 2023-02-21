<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use App\Utils\TiposUsuario;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    public ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    public ?string $plainPassword;

    #[ORM\Column(length: 255)]
    private ?string $nombres = null;

    #[ORM\Column(length: 255)]
    private ?string $apellidoPaterno = null;

    #[ORM\Column(length: 255)]
    private ?string $apellidoMaterno = null;

    #[ORM\Column(length: 255)]
    private ?string $correo = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 18, nullable: true, unique: true)]
    private ?string $curp = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $telefono = null;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Programa::class, orphanRemoval: true)]
    private Collection $programas;

    public function __construct()
    {
        $this->programas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getNombres(): ?string
    {
        return $this->nombres;
    }

    public function setNombres(string $nombres): self
    {
        $this->nombres = $nombres;

        return $this;
    }

    public function getApellidoPaterno(): ?string
    {
        return $this->apellidoPaterno;
    }

    public function setApellidoPaterno(string $apellidoPaterno): self
    {
        $this->apellidoPaterno = $apellidoPaterno;

        return $this;
    }

    public function getApellidoMaterno(): ?string
    {
        return $this->apellidoMaterno;
    }

    public function setApellidoMaterno(string $apellidoMaterno): self
    {
        $this->apellidoMaterno = $apellidoMaterno;

        return $this;
    }

    public function getCorreo(): ?string
    {
        return $this->correo;
    }

    public function setCorreo(string $correo): self
    {
        $this->correo = $correo;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getNombreCompleto(): string
    {
        return "{$this->nombres} {$this->apellidoPaterno} {$this->apellidoMaterno}";
    }

    public function serial(): array
    {
        return [
            "username" => $this->username,
            "nombreCompleto" => $this->getNombreCompleto(),
            "correo" => $this->correo
        ];
    }

    public function getCurp(): ?string
    {
        return $this->curp;
    }

    public function setCurp(?string $curp): self
    {
        $this->curp = $curp;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getType(): TiposUsuario {
        switch($this->roles[0]){
            case "ROLE_ROOT": return TiposUsuario::ROOT;
            case "ROLE_ADMIN": return TiposUsuario::ADMIN;
            case "ROLE_SECRETARIA": return TiposUsuario::SECRETARIA;
            case "ROLE_PROFESOR": return TiposUsuario::PROFESOR;
            case "ROLE_ALUMNO": return TiposUsuario::ALUMNO;
        }
    }
    public function getFormType(): string {
        return "App\\Form\\Type\\".$this->getType()->value."Type";
    }

    /**
     * @return Collection<int, Programa>
     */
    public function getProgramas(): Collection
    {
        return $this->programas;
    }

    public function addPrograma(Programa $programa): self
    {
        if (!$this->programas->contains($programa)) {
            $this->programas->add($programa);
            $programa->setCreatedBy($this);
        }

        return $this;
    }

    public function removePrograma(Programa $programa): self
    {
        if ($this->programas->removeElement($programa)) {
            // set the owning side to null (unless already changed)
            if ($programa->getCreatedBy() === $this) {
                $programa->setCreatedBy(null);
            }
        }

        return $this;
    }
}
