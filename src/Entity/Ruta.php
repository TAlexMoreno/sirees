<?php

namespace App\Entity;

use App\Repository\RutaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RutaRepository::class)]
class Ruta
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255)]
    public ?string $path = null;

    #[ORM\Column(length: 255)]
    public ?string $label = null;

    #[ORM\Column(length: 255)]
    public ?string $name = null;

    #[ORM\Column(length: 255)]
    public ?string $icon = null;

    #[ORM\Column(length: 255)]
    public ?string $minimumRole = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'childs')]
    public ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $childs;

    public function __construct()
    {
        $this->childs = new ArrayCollection();
    }

    public function setId(int $id) 
    {
        $this->id = $id;
        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getMinimumRole(): ?string
    {
        return $this->minimumRole;
    }

    public function setMinimumRole(string $minimumRole): self
    {
        $this->minimumRole = $minimumRole;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChilds(): Collection
    {
        return $this->childs;
    }

    public function addChild(self $child): self
    {
        if (!$this->childs->contains($child)) {
            $this->childs->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->childs->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }
}
