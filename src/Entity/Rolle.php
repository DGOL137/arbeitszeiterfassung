<?php

namespace App\Entity;

use App\Repository\RolleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RolleRepository::class)]
#[ORM\Table(name: 'Rolle')]
class Rolle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'rolle_id', type: 'integer')]
    private ?int $rolle_id = null;

    #[ORM\Column(type: 'string', length: 25)]
    private ?string $bezeichnung = null;

    #[ORM\OneToMany(mappedBy: 'rolle', targetEntity: Mitarbeiter::class)]
    private Collection $mitarbeiter;

    public function __construct()
    {
        $this->mitarbeiter = new ArrayCollection();
    }

    public function getRolleId(): ?int
    {
        return $this->rolle_id;
    }

    public function getBezeichnung(): ?string
    {
        return $this->bezeichnung;
    }

    public function setBezeichnung(string $bezeichnung): static
    {
        $this->bezeichnung = $bezeichnung;
        return $this;
    }

    /**
     * @return Collection<int, Mitarbeiter>
     */
    public function getMitarbeiter(): Collection
    {
        return $this->mitarbeiter;
    }

    public function addMitarbeiter(Mitarbeiter $mitarbeiter): static
    {
        if (!$this->mitarbeiter->contains($mitarbeiter)) {
            $this->mitarbeiter[] = $mitarbeiter;
            $mitarbeiter->setRolle($this);
        }

        return $this;
    }

    public function removeMitarbeiter(Mitarbeiter $mitarbeiter): static
    {
        if ($this->mitarbeiter->removeElement($mitarbeiter)) {
            if ($mitarbeiter->getRolle() === $this) {
                $mitarbeiter->setRolle(null);
            }
        }

        return $this;
    }
}
