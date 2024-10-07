<?php

namespace App\DataFixtures;

use App\Entity\Features;
use App\Entity\Image;
use App\Entity\Room;
use App\Entity\Space;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private const NB_USER = 3;
    private const NB_SPACES = 5;
    private const FEATURES = [
        'Wi-fi',
        'Table de ping pong',
        'Conciergerie',
        'Cuisine moderne',
        'Climatisation',
        'Laverie'
    ];

    public function __construct(
        private UserPasswordHasherInterface $hasher
    )
    {
        
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $features = [];
        foreach (self::FEATURES as $featureName) {
            $feature = new Features();
            $feature->setName($featureName);

            $manager->persist($feature);
            $features[] = $feature;
        }

        $admin = new User;
        $admin
            ->setEmail('admin@mail.com')
            ->setFirstname('Admin')
            ->setLastname('Adminson')
            ->setPassword($this->hasher->hashPassword($admin, 'admin'))
            ->setBirthdate(DateTimeImmutable::createFromMutable($faker->dateTime()))
            ->setAdress($faker->streetAddress())
            ->setCity($faker->city())
            ->setCountry($faker->country())
            ->setPostalcode($faker->postcode())
            ->setRoles(['ROLE_ADMIN']);
        
        $adminImage = new Image;
        $adminImage->setFileName('default-user-profile.jpg');
        $admin->setImage($adminImage);
        
        $manager->persist($adminImage);
        $manager->persist($admin);

        for($i = 0; $i < self::NB_USER; $i++) {
            $user = new User;

            $isOwner = $faker->boolean();

            $user
                ->setEmail($faker->email())
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setPassword($this->hasher->hashPassword($user, 'user'))
                ->setBirthdate(DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setAdress($faker->streetAddress())
                ->setCity($faker->city())
                ->setCountry($faker->country())
                ->setPostalcode($faker->postcode());    
            
            if($isOwner) {
                $user->setRoles(['ROLE_OWNER']);
                $owners[] = $user;
            }

            $userImage = new Image;
            $userImage->setFileName('default-user-profile.jpg');
            $user->setImage($userImage);

            $manager->persist($userImage);
            $manager->persist($user);
            $users[] = $user;
        }

        for($i = 0; $i < self::NB_SPACES; $i++) {
            $space = new Space;
            $space
                ->setName($faker->word())
                ->setDescription($faker->paragraph())
                ->setAdress($faker->streetAddress())
                ->setCity($faker->city())
                ->setCountry($faker->country())
                ->setPostalcode($faker->postcode())
                ->setPrice($faker->randomFloat(2, 100, 500))
                ->setRating($faker->randomFloat(1, 1, 5))
                ->setOwner($owners[random_int(0, count($owners) - 1)]);
                
                foreach ($features as $feature) {
                    if ($faker->boolean(70)) {
                        $space->addFeature($feature);
                    }
                }

                $spaceImage = new Image;
                $spaceImage->setFileName('default-space.jpg');
                $space->addImage($spaceImage);

                $manager->persist($spaceImage);

                $roomNum = random_int(1, 3);
                for ($j = 0; $j < $roomNum; $j++) {
                    $room = new Room;
                    $room
                        ->setName($faker->word())
                        ->setPrice($faker->randomFloat(2, 35, 60))
                        ->setSpace($space);
                    
                    $roomImage = new Image;
                    $roomImage->setFileName('default-room.jpg');
                    $room->addImage($roomImage);

                    $manager->persist($roomImage);

                    $space->addRoom($room);
                    $manager->persist($room);
                }

            $manager->persist($space);
        }

        $manager->flush();
    }
}
