<?php

namespace App\Entity;

use App\Repository\MitarbeiterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: MitarbeiterRepository::class)]
#[ORM\Table(name: 'Mitarbeiter')]
#[UniqueEntity(fields: ['mitarbeiternummer'], message: 'Diese Mitarbeiternummer ist bereits registriert.')]
#[UniqueEntity(fields: ['email'], message: 'Diese E-Mail-Adresse ist bereits registriert.')]
class Mitarbeiter implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'mitarbeiter_id', type: 'integer')]
    private ?int $mitarbeiter_id = null;

    #[ORM\Column(type: 'integer', unique: true)]
    #[Assert\NotBlank(message: 'Bitte gib eine Mitarbeiternummer ein.')]
    #[Assert\Positive(message: 'Die Mitarbeiternummer muss positiv sein.')]
    private ?int $mitarbeiternummer = null;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\NotBlank(message: 'Bitte gib deinen Vornamen ein.')]
    #[Assert\Regex(
        pattern: '/^[\p{L}\-\s]+$/u',
        message: 'Nur Buchstaben, Bindestriche und Leerzeichen sind erlaubt.'
    )]
    private ?string $vorname = null;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\NotBlank(message: 'Bitte gib deinen Nachnamen ein.')]
    #[Assert\Regex(
        pattern: '/^[\p{L}\-\s]+$/u',
        message: 'Nur Buchstaben, Bindestriche und Leerzeichen sind erlaubt.'
    )]
    private ?string $nachname = null;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    #[Assert\NotBlank(message: 'Bitte gib deine E-Mail-Adresse ein.')]
    #[Assert\Email(message: 'Bitte gib eine gültige E-Mail-Adresse ein.')]
    private ?string $email = null;

    #[ORM\Column(name: 'passworthash', type: 'string', length: 255)]
    private ?string $passworthash = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $registrierungsdatum = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $letzteaenderung = null;

    #[ORM\ManyToOne(targetEntity: Rolle::class, inversedBy: 'mitarbeiter')]
    #[ORM\JoinColumn(name: 'rolle_id', referencedColumnName: 'rolle_id', nullable: false)]
    private ?Rolle $rolle = null;

    #[ORM\ManyToOne(targetEntity: DatenschutzStatus::class, inversedBy: 'mitarbeiter')]
    #[ORM\JoinColumn(name: 'ds_id', referencedColumnName: 'ds_id', nullable: false)]
    private ?DatenschutzStatus $datenschutzStatus = null;

    #[ORM\OneToMany(mappedBy: 'mitarbeiter', targetEntity: Arbeitszeit::class, orphanRemoval: true)]
    private Collection $arbeitszeiten;

    public function __construct()
    {
        $this->arbeitszeiten = new ArrayCollection();
    }

    public function getMitarbeiterId(): ?int
    {
        return $this->mitarbeiter_id;
    }

    public function getMitarbeiternummer(): ?int
    {
        return $this->mitarbeiternummer;
    }

    public function setMitarbeiternummer(int $mitarbeiternummer): static
    {
        $this->mitarbeiternummer = $mitarbeiternummer;
        return $this;
    }

    public function getVorname(): ?string
    {
        return $this->vorname;
    }

    public function setVorname(string $vorname): static
    {
        $this->vorname = $vorname;
        return $this;
    }

    public function getNachname(): ?string
    {
        return $this->nachname;
    }

    public function setNachname(string $nachname): static
    {
        $this->nachname = $nachname;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPassworthash(): ?string
    {
        return $this->passworthash;
    }

    public function setPassworthash(string $passworthash): static
    {
        $this->passworthash = $passworthash;
        return $this;
    }

    public function getRegistrierungsdatum(): ?\DateTimeInterface
    {
        return $this->registrierungsdatum;
    }

    public function setRegistrierungsdatum(\DateTimeInterface $registrierungsdatum): static
    {
        $this->registrierungsdatum = $registrierungsdatum;
        return $this;
    }

    public function getLetzteaenderung(): ?\DateTimeInterface
    {
        return $this->letzteaenderung;
    }

    public function setLetzteaenderung(?\DateTimeInterface $letzteaenderung): static
    {
        $this->letzteaenderung = $letzteaenderung;
        return $this;
    }

    public function getRolle(): ?Rolle
    {
        return $this->rolle;
    }

    public function setRolle(?Rolle $rolle): static
    {
        $this->rolle = $rolle;
        return $this;
    }

    public function getDatenschutzStatus(): ?DatenschutzStatus
    {
        return $this->datenschutzStatus;
    }

    public function setDatenschutzStatus(?DatenschutzStatus $datenschutzStatus): static
    {
        $this->datenschutzStatus = $datenschutzStatus;
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
            $arbeitszeit->setMitarbeiter($this);
        }

        return $this;
    }

    public function removeArbeitszeit(Arbeitszeit $arbeitszeit): static
    {
        if ($this->arbeitszeiten->removeElement($arbeitszeit)) {
            if ($arbeitszeit->getMitarbeiter() === $this) {
                $arbeitszeit->setMitarbeiter(null);
            }
        }

        return $this;
    }

    // Symfony Security
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = [];

        if ($this->rolle && $this->rolle->getBezeichnung()) {
            $rollenName = 'ROLE_' . strtoupper(str_replace(' ', '_', $this->rolle->getBezeichnung()));
            $roles[] = $rollenName;
        }

        // Füge immer die Standardrolle hinzu
        $roles[] = 'ROLE_MITARBEITER';

        return array_unique($roles);
    }

    public function getPassword(): string
    {
        return $this->passworthash ?? '';
    }



    // Alias-Methode, da Symfony Security englische Methoden/Variablen erwartet
    public function setPassword(string $passwort): static
    {
        $this->passworthash = $passwort;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
