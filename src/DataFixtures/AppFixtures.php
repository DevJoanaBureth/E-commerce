<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    protected $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setNom('Bureth')->setPrenom('Joana')->setEmail('brth.joana@gmail.com')->setRoles(['ROLES_USER']);
        $encoded = $this->encoder->hashPassword($user, '123');
        $user->setPassword($encoded);

        $manager->persist($user);
        $manager->flush();
    }
}
