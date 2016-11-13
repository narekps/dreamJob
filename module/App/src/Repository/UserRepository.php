<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityManager;
use App\Entity\User as UserEntity;

class UserRepository extends BaseEntityRepository
{

    protected $alias = 'user';

    protected $searchColumn = 'name';

    public function read($asArray = true)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select($this->alias)->from('AopAdmin\Entity\User', $this->alias);

        $qb = $this->applyFilter($qb);

        $result = $qb->getQuery()->getResult();
        if ($asArray === true) {
            $users = [];
            /** @var UserEntity $user */
            foreach ($result as $user) {
                $users[] = $this->getArrayCopy($user);
            }

            return $users;
        }

        return $result;
    }

    public function getTotal()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select("COUNT({$this->alias}.id)")->from('AopAdmin\Entity\User', $this->alias);

        return $qb->getQuery()->getSingleScalarResult();
    }


    /**
     * @param UserEntity $entity
     *
     * @return array
     */
    public function getArrayCopy($entity)
    {
        $arr = [];
        $arr['id'] = (int)$entity->getId();
        $arr['name'] = $entity->getDisplayName();
        $arr['password'] = '';
        $arr['email'] = $entity->getIdentity();
        $arr['displayName'] = $entity->getDisplayName();
        $arr['name'] = $entity->getDisplayName();

        return $arr;
    }
}
