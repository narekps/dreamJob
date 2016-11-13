<?php

namespace App\Factory\Repository;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use App\Repository\UserRepository;

class User implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $container->get('Doctrine\ORM\EntityManager');
        /** @var \App\Repository\BaseEntityRepository $rep */
        $rep = new UserRepository($em, $em->getClassMetadata('App\Entity\User'));

        return $rep;
    }
}
