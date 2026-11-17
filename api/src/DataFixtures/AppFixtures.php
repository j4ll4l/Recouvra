<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Story\PrimaryTenantStory;
use App\Story\SecondaryTenantStory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        PrimaryTenantStory::load();
        SecondaryTenantStory::load();

    }
}
