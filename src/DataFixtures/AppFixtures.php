<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\ProductVariant;
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
        $adminUser->setEmail("imennaija17@gmail.com")
            ->setRoles(["ROLE_ADMIN"])
            ->setFirstName("Imen")
            ->setLastName("Naija")
            ->setPhone(0)
            ->setPassword($this->hasher->hashPassword($adminUser, 'a'));
        $manager->persist($adminUser);

        $regularUser = new User();
        $regularUser->setEmail("amaninaija@gmail.com")
            ->setFirstName("Amani")
            ->setLastName("Naija")
            ->setPhone(1)
            ->setPassword($this->hasher->hashPassword($adminUser, "b"));
        $manager->persist($regularUser);

        $categoryA = new Category();
        $categoryA->setName("Clothes");
        $manager->persist($categoryA);

        $categoryB = new Category();
        $categoryB->setName("Tops")
            ->setParent($categoryA);
        $manager->persist($categoryB);

        $categoryC = new Category();
        $categoryC->setName("Cardigans")
            ->setParent($categoryB);
        $manager->persist($categoryC);

        $categoryD = new Category();
        $categoryD->setName("Bras")
            ->setParent($categoryB);
        $manager->persist($categoryD);

        $categoryE = new Category();
        $categoryE->setName("Accessories");
        $manager->persist($categoryE);

        $categoryF = new Category();
        $categoryF->setName("Phone Cases")
            ->setParent($categoryE);
        $manager->persist($categoryF);

        $categoryG = new Category();
        $categoryG->setName("Bags")
            ->setParent($categoryE);
        $manager->persist($categoryG);

        $productA = new Product();
        $productA->setName("Phone case A")
            ->setCategory($categoryE)
            ->setDescription("Very nice")
            ->setPrice(1000);
        $manager->persist($productA);

        $productVariantA = new ProductVariant();
        $productVariantA->setProduct($productA)
            ->setColor("Blue")
            ->setSize("XL")
            ->setStock(10);
        $manager->persist($productVariantA);

        $manager->flush();
    }
}
