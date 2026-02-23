<?php

namespace App\DataFixtures;

use App\Entity\DatenschutzStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DatenschutzStatusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $zugestimmt = new DatenschutzStatus();
        $zugestimmt->setBezeichnung('zugestimmt');
        $manager->persist($zugestimmt);

        $nichtZugestimmt = new DatenschutzStatus();
        $nichtZugestimmt->setBezeichnung('nicht zugestimmt');
        $manager->persist($nichtZugestimmt);

        $manager->flush();
    }
}
