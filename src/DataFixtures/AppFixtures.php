<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {

        $adminUser = new User();
        $adminUser->setEmail("imennaija17@gmail.com");
        $adminUser->setRoles(["ROLE_ADMIN"]);
        $adminUser->setPassword($this->hasher->hashPassword($adminUser, 'a'));
        $manager->persist($adminUser);

        $regularUser = new User();
        $regularUser->setEmail("amaninaija@gmail.com");
        $regularUser->setPassword($this->hasher->hashPassword($adminUser, "b"));
        $manager->persist($regularUser);

        $manager->flush();
    }
}
