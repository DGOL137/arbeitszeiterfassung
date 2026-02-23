<?php

namespace App\Entity;

use App\Repository\ProjektRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjektRepository::class)]
#[ORM\Table(name: 'Projekt')]
class Projekt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'projekt_id', type: 'integer')]
    private ?int $projekt_id = null;

    #[ORM\Column(type: 'integer', length: 5)]
    private ?int $projektnummer = null;

    #[ORM\OneToMany(mappedBy: 'projekt', targetEntity: Arbeitszeit::class)]
    private Collection $arbeitszeiten;

    public function __construct()
    {
        $this->arbeitszeiten = new ArrayCollection();
    }

    public function getProjektId(): ?int
    {
        return $this->projekt_id;
    }

    public function getProjektnummer(): ?int
    {
        return $this->projektnummer;
    }

    public function setProjektnummer(int $projektnummer): static
    {
        $this->projektnummer = $projektnummer;
        return $this;
    }

    /**
     * @return Collection<int, Arbeitszeit>
     */
    public function getArbeitszeiten(): Collection
    {
        return $this->arbeitszeiten;
    }

    public function addArbeitszeit(Arbeitszeit $arbeitszeit): static
    {
        if (!$this->arbeitszeiten->contains($arbeitszeit)) {
            $this->arbeitszeiten[] = $arbeitszeit;
            $arbeitszeit->setProjekt($this);
        }

        return $this;
    }

    public function removeArbeitszeit(Arbeitszeit $arbeitszeit): static
    {
        if ($this->arbeitszeiten->removeElement($arbeitszeit)) {
            if ($arbeitszeit->getProjekt() === $this) {
                $arbeitszeit->setProjekt(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->projekt_id;
    }
}
