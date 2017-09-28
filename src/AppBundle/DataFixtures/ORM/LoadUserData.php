<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use AppBundle\Entity\User;
use AppBundle\Entity\Role;

class LoadUserData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface {
    
     /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    /**
     * Load Post data
     */
    public function load(ObjectManager $manager)
    {   
        $role1 = new Role();
        $role1->setName('admin');
        $role1->setRole('ROLE_ADMIN');
        
        $role2 = new Role();
        $role2->setName('user');
        $role2->setRole('ROLE_USER');
                
        $user = new User();
        
        $user->setName('admin');
        $user->setSurname('adminsurname');
        $user->setIsActive(true);
        $user->setEmail('admin@gmail.com');
        $user->setPhone('+12345678');
        $user->addRole($role1);

        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword('admin', $user->getSalt());
        $user->setPassword($password);
        
        $this->addReference('admin', $user);
        
        $manager->persist($role1);
        $manager->persist($role2);
        $manager->persist($user);
        $manager->flush();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}