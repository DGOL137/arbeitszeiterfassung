<?php

namespace App\DataFixtures;

use App\Entity\Rolle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RolleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $mitarbeiter = new Rolle();
        $mitarbeiter->setBezeichnung('Mitarbeiter');
        $manager->persist($mitarbeiter);

        $personalsachbearbeitung = new Rolle();
        $personalsachbearbeitung->setBezeichnung('Personalsachbearbeitung');
        $manager->persist($personalsachbearbeitung);

        $projektsachbearbeitung = new Rolle();
        $projektsachbearbeitung->setBezeichnung('Projektsachbearbeitung');
        $manager->persist($projektsachbearbeitung);

        $manager->flush();
    }
}
