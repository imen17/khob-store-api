<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Photo;
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
        $categoryA->setName("Accessories");
        $manager->persist($categoryA);

        $categoryB = new Category();
        $categoryB->setName("Earrings")
            ->setParent($categoryA);
        $manager->persist($categoryB);

        $product = new Product();
        $product->setName("Velvet Whispers")
            ->setCategory($categoryB)
            ->setDescription("A pair of enchanting dreamy violet pastel earrings. These beauties are a delicate blend of charm handcrafted with love and a touch of wanderlust, these earrings add a free-spirited flair to your style. 💜✨
Available now to infuse your look with a hint of bohemian beauty.")
            ->setPrice(1500);

        $manager->persist($product);

        $variant = new ProductVariant();
        $variant->setProduct($product)
            ->setColor("Pink")
            ->setSize("Small")
            ->setStock(2);
        $manager->persist($variant);
        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/D4z900V/img-4.jpg");

        $manager->persist($photo);

        ####

        $categoryB = new Category();
        $categoryB->setName("Bags")
            ->setParent($categoryA);
        $manager->persist($categoryB);

        $categoryC = new Category();
        $categoryC->setName("Tote Bags")
            ->setParent($categoryB);
        $manager->persist($categoryC);

        $product = new Product();
        $product->setName("Emerald Charm")
            ->setCategory($categoryC)
            ->setDescription("A petite shoulder handbag in a deep, plain dark green hue. This handcrafted accessory blends style and convenience seamlessly. 💚 Elevate your on-the-go look with a touch of sophistication. Add a dash of emerald charm to your daily adventures.")
            ->setPrice(7500);
        $manager->persist($product);

        $variant = new ProductVariant();
        $variant->setProduct($product)
            ->setColor("Green")
            ->setSize("Small")
            ->setStock(3);
        $manager->persist($variant);
        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/F4FGnwx/img-5.jpg");
        $manager->persist($photo);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/kKVJmLL/401148140-1288480378518995-5805948994432808492-n.jpg");
        $manager->persist($photo);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/QJKkzBB/400958439-865793558584750-2213782260705161747-n.jpg");
        $manager->persist($photo);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/bvPZRFJ/401075548-2017501191966293-7257245215410912608-n.jpg");
        $manager->persist($photo);

        ####

        $product = new Product();
        $product->setName("Blush Bloom")
            ->setCategory($categoryC)
            ->setDescription("🌸")
            ->setPrice(5000);
        $manager->persist($product);

        $variant = new ProductVariant();
        $variant->setProduct($product)
            ->setColor("Pink")
            ->setSize("Medium")
            ->setStock(1);
        $manager->persist($variant);
        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/nw72qCk/img-7.jpg");
        $manager->persist($photo);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/ySmqtFC/402871089-339177978706726-3192757479904456333-n.jpg");
        $manager->persist($photo);

        ###

        $categoryB = new Category();
        $categoryB->setName("Headscarves")
            ->setParent($categoryA);
        $manager->persist($categoryB);

        $product = new Product();
        $product->setName("Zestique")
            ->setCategory($categoryB)
            ->setDescription("Elevate your style with our collection: a handcrafted orange bandana and top.")
            ->setPrice(2500);
        $manager->persist($product);

        $variant = new ProductVariant();
        $variant->setProduct($product)
            ->setColor("Orange")
            ->setSize("Small")
            ->setStock(1);
        $manager->persist($variant);
        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/bWHxdLW/402751596-1529753587787830-6412437514001442130-n.jpg");
        $manager->persist($photo);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/VpwScNm/403844024-3701634370159760-2639627633903479271-n.jpg");
        $manager->persist($photo);

        ###
        $product = new Product();
        $product->setName("Aether")
            ->setCategory($categoryB)
            ->setDescription("🤍")
            ->setPrice(3000);
        $manager->persist($product);

        $variant = new ProductVariant();
        $variant->setProduct($product)
            ->setColor("White")
            ->setSize("Medium")
            ->setStock(1);
        $manager->persist($variant);
        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/GTqrHsn/400447047-716265883752831-6965097729698010143-n.jpg");
        $manager->persist($photo);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/yPjmbRB/399749918-6639075499549074-5437531355287375692-n.jpg");
        $manager->persist($photo);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/Ld4btYr/399556974-1365640734355680-4992875045374225178-n.jpg");
        $manager->persist($photo);


        ###

        $categoryA = new Category();
        $categoryA->setName("Tops");
        $manager->persist($categoryA);

        $categoryB = new Category();
        $categoryB->setName("Cardigans")
            ->setParent($categoryA);
        $manager->persist($categoryB);

        $product = new Product();
        $product->setName("Fika")
            ->setCategory($categoryB)
            ->setDescription("Indulge in the cozy charm of 'Fika.' Just like the Swedish tradition of slowing down with coffee and pastries, this patchwork cardigan invites you to embrace life's simple joys. With its warm hues of beige, brown, and white, it's the perfect companion for leisurely moments and shared laughter. Treat yourself to a moment of comfort and connection.
DM for more details")
            ->setPrice(13990);
        $manager->persist($product);

        $variant = new ProductVariant();
        $variant->setProduct($product)
            ->setColor("Brown")
            ->setSize("Small")
            ->setStock(1);
        $manager->persist($variant);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/446Q3rn/425829153-1515837222528186-3526670001558086179-n.jpg");
        $manager->persist($photo);

        ####

        $categoryB = new Category();
        $categoryB->setName("Crop Tops")
            ->setParent($categoryA);
        $manager->persist($categoryB);

        $product = new Product();
        $product->setName("Luminara")
            ->setCategory($categoryB)
            ->setDescription("The vibrancy of the color and the intricate details shine in every stitch. 💙 'Luminara' is available now to add a spark of brilliance to your wardrobe.")
            ->setPrice(8500);
        $manager->persist($product);

        $variant = new ProductVariant();
        $variant->setProduct($product)
            ->setColor("Blue")
            ->setSize("Medium")
            ->setStock(1);
        $manager->persist($variant);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/Bwg2mgS/404594197-1063866261699424-7857658418019939595-n.jpg");
        $manager->persist($photo);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/DpQ4k39/403823906-316336444588560-756827689465182532-n.jpg");
        $manager->persist($photo);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/f2CK6qJ/402543968-866993945078818-6089849169109166981-n.jpg");
        $manager->persist($photo);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/6ZK2P7T/394001181-712673803734415-3085385319865469462-n.jpg");
        $manager->persist($photo);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/St8mpWW/401687011-1527820397954737-1835709738650017258-n.jpg");
        $manager->persist($photo);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/28G4Btj/401144423-645584094439018-5353414438656582322-n.jpg");
        $manager->persist($photo);

        $product = new Product();
        $product->setName("Morii")
            ->setCategory($categoryB)
            ->setDescription("A creation that exudes an air of mystery and elegance. Inspired by the enigmatic beauty of life's hidden moments, this handcrafted crochet piece is a testament to the art of subtlety. 'Morii' embraces the idea of finding beauty in the unseen, embodying a quiet confidence that speaks volumes. Like its namesake, this top is a reminder that sometimes, less is more.
Available now, it invites you to experience a touch of allure and sophistication in every wear.")
            ->setPrice(7000);
        $manager->persist($product);

        $variant = new ProductVariant();
        $variant->setProduct($product)
            ->setColor("Brown")
            ->setSize("Small")
            ->setStock(1);
        $manager->persist($variant);
        $variant = new ProductVariant();
        $variant->setProduct($product)
            ->setColor("Brown")
            ->setSize("Medium")
            ->setStock(1);
        $manager->persist($variant);
        $variant = new ProductVariant();
        $variant->setProduct($product)
            ->setColor("Brown")
            ->setSize("Large")
            ->setStock(1);
        $manager->persist($variant);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/nrFdgmR/387268359-245399048118125-7741126407115648505-n.jpg");
        $manager->persist($photo);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/zHtbkrg/387637947-1257482828977650-979320199278167537-n.jpg");
        $manager->persist($photo);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/VHmwTFQ/387693477-333911549131806-8060056716738375415-n.jpg");
        $manager->persist($photo);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/gW11H5G/387708620-693484552691765-6652403190914802736-n.jpg");
        $manager->persist($photo);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/7kgHDhr/403798774-1255322835139908-4385192302353714552-n.jpg");
        $manager->persist($photo);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/GVkHXHR/403919684-1345161359722390-5189284609748732705-n.jpg");
        $manager->persist($photo);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/nkL1ypn/404654510-6110419675727322-315437616853783297-n.jpg");
        $manager->persist($photo);

        ###
        $product = new Product();
        $product->setName("Lagom")
            ->setCategory($categoryB)
            ->setDescription("I wanted to make something that just screams 'perfectly-simple' and Lagom top does that.
Lagom is a beautiful Swedish word,
that means having just the right amount; not too much, not too little, just right.
i'm so happy with the end result.
your Lagom top is available now🎉")
            ->setPrice(9500);
        $manager->persist($product);

        $variant = new ProductVariant();
        $variant->setProduct($product)
            ->setColor("Grey")
            ->setSize("Small")
            ->setStock(1);
        $manager->persist($variant);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/r346CpX/381330046-644346181172258-8707058680501363140-n.jpg");
        $manager->persist($photo);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/sJdRnVL/382578668-6320880101349385-4796797423923578524-n.jpg");
        $manager->persist($photo);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/MRC2M7y/382569907-821078603092377-1402700716435330603-n.jpg");
        $manager->persist($photo);

        ###

        $product = new Product();
        $product->setName("Zestique")
            ->setCategory($categoryB)
            ->setDescription("Introducing 'Zestique' 🍊
Elevate your style with our collection: a handcrafted orange bandana and top.
DM to infuse a burst of zest into your wardrobe! 😉")
            ->setPrice(5000);
        $manager->persist($product);

        $variant = new ProductVariant();
        $variant->setProduct($product)
            ->setColor("Orange")
            ->setSize("Extra Small")
            ->setStock(1);
        $manager->persist($variant);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/thtDqfg/390899003-167933252976336-8223561286198522151-n.jpg");
        $manager->persist($photo);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/CMQgyMg/387702624-777995240764061-3246786012279598606-n.jpg");
        $manager->persist($photo);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/pL2G3Tv/387654684-1704730136619239-8832520904696455451-n.jpg");
        $manager->persist($photo);

        ###

        $categoryA = new Category();
        $categoryA->setName("Leg Warmers");
        $manager->persist($categoryA);

        $product = new Product();
        $product->setName("Frosty Serenity")
            ->setCategory($categoryA)
            ->setDescription("❄️🔥")
            ->setPrice(5500);
        $manager->persist($product);

        $variant = new ProductVariant();
        $variant->setProduct($product)
            ->setColor("White")
            ->setSize("Medium")
            ->setStock(1);
        $manager->persist($variant);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/TkSHNWz/417796606-712363684330700-7668442743810268008-n.jpg");
        $manager->persist($photo);

        $photo = new Photo();
        $photo->setProduct($product)
            ->setUrl("https://i.ibb.co/HKrJ75v/417405493-334406159466742-3986094438132017895-n.jpg");
        $manager->persist($photo);

        $manager->flush();
    }
}
