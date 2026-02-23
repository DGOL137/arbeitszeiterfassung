<?php

namespace App\Entity;

use App\Repository\ArbeitszeitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArbeitszeitRepository::class)]
#[ORM\Table(name: 'Arbeitszeit')]
class Arbeitszeit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'arbeitszeit_id', type: 'integer')]
    private ?int $arbeitszeit_id = null;

    #[ORM\ManyToOne(targetEntity: Mitarbeiter::class, inversedBy: 'arbeitszeiten')]
    #[ORM\JoinColumn(name: 'mitarbeiter_id', referencedColumnName: 'mitarbeiter_id', nullable: false)]
    private ?Mitarbeiter $mitarbeiter = null;

    #[ORM\ManyToOne(inversedBy: 'arbeitszeiten')]
    #[ORM\JoinColumn(name: 'projekt_id', referencedColumnName: 'projekt_id', nullable: false)]
    private ?Projekt $projekt = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $datum = null;

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTimeInterface $startzeit = null;

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTimeInterface $endzeit = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $bemerkung = null;

    // ---------- Getter & Setter ----------

    public function getArbeitszeitId(): ?int
    {
        return $this->arbeitszeit_id;
    }

    public function getMitarbeiter(): ?Mitarbeiter
    {
        return $this->mitarbeiter;
    }

    public function setMitarbeiter(?Mitarbeiter $mitarbeiter): static
    {
        $this->mitarbeiter = $mitarbeiter;
        return $this;
    }

    public function getProjekt(): ?Projekt
    {
        return $this->projekt;
    }

    public function setProjekt(?Projekt $projekt): static
    {
        $this->projekt = $projekt;
        return $this;
    }

    public function getDatum(): ?\DateTimeInterface
    {
        return $this->datum;
    }

    public function setDatum(\DateTimeInterface $datum): static
    {
        $this->datum = $datum;
        return $this;
    }

    public function getStartzeit(): ?\DateTimeInterface
    {
        return $this->startzeit;
    }

    public function setStartzeit(\DateTimeInterface $startzeit): static
    {
        $this->startzeit = $startzeit;
        return $this;
    }

    public function getEndzeit(): ?\DateTimeInterface
    {
        return $this->endzeit;
    }

    public function setEndzeit(\DateTimeInterface $endzeit): static
    {
        $this->endzeit = $endzeit;
        return $this;
    }

    public function getBemerkung(): ?string
    {
        return $this->bemerkung;
    }

    public function setBemerkung(?string $bemerkung): static
    {
        $this->bemerkung = $bemerkung;
        return $this;
    }
}
