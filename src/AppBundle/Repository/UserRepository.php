<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository{
    
    public function findActive(){
        return $this->getEntityManager()
            ->createQuery(
                'SELECT u FROM AppBundle:User u WHERE u.isActive = :active'
            )
            ->setParameter('active', true)
            ->getResult();
    }
    
}