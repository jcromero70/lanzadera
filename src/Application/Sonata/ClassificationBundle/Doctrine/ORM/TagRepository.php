<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 6/4/15
 * Time: 11:31
 */

namespace Application\Sonata\ClassificationBundle\Doctrine\ORM;


use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class TagRepository extends EntityRepository
{
    /**
     * @param mixed $id
     *
     * @return null|object
     */
    public function find($id)
    {
        if (is_array($id)) {
            $id = current($id);
        }

        return parent::find($id);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getAll()
    {
        return $this
            ->createQueryBuilder('o')
            ->orderBy('o.name', 'ASC')
        ;
    }
}