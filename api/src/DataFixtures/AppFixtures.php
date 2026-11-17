<?php

namespace App\DataFixtures;

use App\Story\PrimaryTenantStory;
use App\Story\SecondaryTenantStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        PrimaryTenantStory::load();
        SecondaryTenantStory::load();
    }
}
