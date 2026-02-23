<?php

namespace App\Entity;

use App\Repository\DatenschutzStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DatenschutzStatusRepository::class)]
#[ORM\Table(name: 'Datenschutz_Status')]
class DatenschutzStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ds_id', type: 'integer')]
    private ?int $ds_id = null;

    #[ORM\Column(length: 20)]
    private ?string $bezeichnung = null;

    #[ORM\OneToMany(mappedBy: 'datenschutzStatus', targetEntity: Mitarbeiter::class)]
    private Collection $mitarbeiter;

    public function __construct()
    {
        $this->mitarbeiter = new ArrayCollection();
    }

    public function getDsId(): ?int
    {
        return $this->ds_id;
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
            $mitarbeiter->setDatenschutzStatus($this);
        }

        return $this;
    }

    public function removeMitarbeiter(Mitarbeiter $mitarbeiter): static
    {
        if ($this->mitarbeiter->removeElement($mitarbeiter)) {
            // Setze die Beziehung nur zurück, wenn sie aktuell auf dieses Objekt zeigt
            if ($mitarbeiter->getDatenschutzStatus() === $this) {
                $mitarbeiter->setDatenschutzStatus(null);
            }
        }

        return $this;
    }
}
