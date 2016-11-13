<?php

namespace App\Repository;

use Doctrine\ORM\Query as ORMQuery;
use Doctrine\ORM\EntityRepository;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Cache\RedisCache as RedisCacheDriver;
use Doctrine\ORM\Cache\QueryCacheKey;

abstract class BaseEntityRepository extends EntityRepository
{

    /**
     * используется в QueryBuilder'е
     *
     * @var string
     */
    protected $alias = 'ALIAS';

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param \Doctrine\ORM\EntityManager         $em
     * @param \Doctrine\ORM\Mapping\ClassMetadata $class
     */
    public function __construct(\Doctrine\ORM\EntityManager $em, \Doctrine\ORM\Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);
    }

    /**
     * @param Object $entity
     *
     * @return array
     */
    abstract public function getArrayCopy($entity);

    public function getEntityManager()
    {
        return parent::getEntityManager();
    }

}
