<?php

namespace App\Factory\Repository;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use App\Repository\FileRepository;

class File implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $container->get('Doctrine\ORM\EntityManager');
        /** @var \App\Repository\BaseEntityRepository $rep */
        $rep = new FileRepository($em, $em->getClassMetadata('App\Entity\File'));

        return $rep;
    }
}
