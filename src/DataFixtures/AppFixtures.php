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
        $encodedUser = $this->encoder->hashPassword($user, '123');
        $user->setNom('Bureth')
        ->setPrenom('Joana')
        ->setEmail('brth.joana@gmail.com')
        ->setRoles(['ROLES_USER'])
        ->setPassword($encodedUser);

        $admin = new User();
        $encodedAdmin = $this->encoder->hashPassword($admin, '123');
        $admin->setNom('Bureth')
        ->setPrenom('Joana')
        ->setEmail('admin@gmail.com')
        ->setRoles(['ROLES_ADMIN'])
        ->setPassword($encodedAdmin);

        $employee = new User();
        $encodedEmployee = $this->encoder->hashPassword($employee, '123');
        $employee->setNom('Doe')
        ->setPrenom('John')
        ->setEmail('john.doe@gmail.com')
        ->setRoles(['ROLES_EMPLOYEE'])
        ->setPassword($encodedEmployee);

        $manager->persist($user);
        $manager->persist($admin);
        $manager->persist($employee);

        $manager->flush();
    }
}
