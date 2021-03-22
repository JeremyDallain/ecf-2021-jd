<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Event;
use App\Entity\Comment;
use Bezhanov\Faker\Provider\Commerce;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));

        $users = [];

        for ($u=1; $u <= 10; $u++) { 
            
            $user = new User();
            $user->setLogin($faker->userName())
                ->setPseudo($faker->name())
                ->setRoles(['ROLE_USER'])
                ->setPassword($this->encoder->encodePassword($user, "password"));

            $users[] = $user;            
            $manager->persist($user);
                        
            for ($e = 0; $e < mt_rand(2, 4); $e++) { 
                
                $event = new Event();
                $event->setTitle($faker->department())
                    ->setDescription($faker->paragraphs(5, true))
                    ->setStartedAt($faker->dateTimeBetween('+1 month', '+2 month'))
                    ->setPicture('image_default.jpg')
                    ->setUser($user);                
                $manager->persist($event);

                for ($c = 0; $c < mt_rand(0, 3); $c++) { 
                
                    $comment = new Comment();
                    $comment->setCreatedAt($faker->dateTimeBetween('-1 month', 'now'))
                        ->setContent($faker->sentence(10, true))
                        ->setEvent($event)
                        ->setUser($faker->randomElement($users));                
                    $manager->persist($comment);  
                }
            } 
        }
        $manager->flush();    
    }
}
