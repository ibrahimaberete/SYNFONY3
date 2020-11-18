<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Booking;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder = $encoder;
    }




    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');
          // Roles 
          $adminRole = new Role();
          $adminRole->setTitle('ROLE_ADMIN');
          $manager->persist($adminRole);
  
          $adminUser = new User();
          $adminUser->setFirstName('ibrahima')
                    ->setLastName('berete')
                    ->setEmail('ibrahima.berete18@gmail.com')
                    ->setHash($this->encoder->encodePassword($adminUser, 'password'))
                    ->setPicture('https://static.nationalgeographic.fr/files/styles/image_3200/public/01-lion-populations-nationalgeographic_1777804.jpg?w=1200&h=800')
                    ->setIntroduction($faker->sentence())
                    ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>')
                    ->addUserRole($adminRole);
                  
          $manager->persist($adminUser);

        // nous gerons les utilisateurs 
        $users = [];
        $genres = ['male', 'female']; 

        for($i = 1; $i <= 10; $i++){
            $user = new User();

            $genre = $faker-> randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 99) .'.jpg';

            
            $picture .= ($genre == 'male' ? 'men/' : 'women/') .$pictureId;

            $hash = $this->encoder->encodePassword($user, 'password');
            






            $user->setFirstName($faker->firstname($genre))
                 ->setLastName($faker->lastname)
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence())
                 ->setDescription('<p>' . join ('</p><p>', $faker->paragraphs(3)) . '</p>')
                 ->setHash($hash)
                 ->setPicture($picture);


            $manager->persist($user);
            $users[] = $user; 

        }



        // Nous gérons les annonces 
        for($i = 1; $i <= 30; $i++){
            $ad = new Ad();

            $title = $faker->sentence();
           
            $coverImage = $faker->imageUrl(1000,350);
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join ('</p><p>',$faker->paragraphs(5)) . '</p>';


            $user = $users[mt_rand(0, count($users)-1)];

            $ad->setTitle($title)
            
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction )
                ->setContent( $content)
                ->setPrice(mt_rand(100, 500))
                ->setRooms(mt_rand(1, 5))
                ->setAuthor($user);

            for($j = 1; $j<= mt_rand(2,5); $j++){
                $image = new Image();

                $image->setUrl($faker->imageUrl())
                      ->setCaption($faker->sentence())
                      ->setAd($ad);

                $manager->persist($image);
            }

            // Gestion des reservations
            for($j = 1; $j <= mt_rand(0,10); $j++){
                $booking = new Booking();
                $createdAt = $faker->dateTimeBetween('-6 months');
                $startDate = $faker->dateTimeBetween('-3 months');
                // Gestin de la date de fiin 
                $duration = mt_rand(3, 10);
                $endDate  =(clone $startDate)->modify("+$duration days");
                $amount   = $ad->getPrice() * $duration;
                $booker   = $users[mt_rand(0, count($users) -1)];
                $comment  = $faker->paragraph();
                $booking->setBooker($booker)
                        ->setAd($ad)
                        ->setStartDate($startDate)
                        ->setEndDate($endDate)
                        -> setCreatedAt($createdAt)
                        -> setAmount($amount)
                        ->setComment($comment);

                        $manager->persist($booking);
            }

             $manager->persist($ad);
        }
        

        $manager->flush();
    }
}
