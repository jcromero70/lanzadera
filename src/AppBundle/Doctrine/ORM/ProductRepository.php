<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 22/08/14
 * Time: 07:34
 */

namespace AppBundle\Doctrine\ORM;

use AppBundle\Entity\Product;

class ProductRepository extends CustomRepository
{
	private function getAllAvailableProductsQuery()
	{
		$queryBuilder = $this->getCollectionQueryBuilder();
		$query = $queryBuilder
			->leftJoin('o.organization', 'organization')
			->leftJoin('o.certificates', 'certificates')
			->leftJoin('certificates.classification', 'classification')
			->leftJoin('o.tags', 'tag')
			->where($queryBuilder->expr()->eq('o.status', ':status'))
			->andWhere($queryBuilder->expr()->eq('organization.enabled', ':enabled'))
			->orderBy('o.name', 'asc')
			->setParameter(':status', Product::STATUS_APPROVED)
			->setParameter('enabled', 1)
		;

		return $query;
	}

	public function findAllAvailableProducts()
	{
		return $this->getPaginator($this->getAllAvailableProductsQuery());
	}

	public function findAllAvailableProductsByOrganizationSlug($slug)
	{
		$query = $this->getAllAvailableProductsQuery();

		$query
			->andWhere($query->expr()->eq('organization.slug', ':slug'))
			->setParameter('slug', $slug)
		;

		return $this->getPaginator($query);
	}

	public function findAllAvailableProductsByKeyword($keyword)
	{
		$query = $this->getAllAvailableProductsQuery();

		$query
			->andWhere($query->expr()->orX(
				$query->expr()->like('tag.name', ':keyword'),
				$query->expr()->like('o.name', ':keyword')
			))
			->setParameter('keyword', '%' . $keyword . '%')
		;

		return $this->getPaginator($query);
	}

	public function findAllAvailableProductsByCategorySlug($slug)
	{
		$query = $this->getAllAvailableProductsQuery();

		$query
			->leftJoin('o.category', 'category')
			->andWhere($query->expr()->eq('category.slug', ':slug'))
			->setParameter('slug', $slug)
		;

		return $this->getPaginator($query);
	}

    /**
     * Busca todos los productos de una organización que superen el valor mínimo de una clasificación
     *
     * @param $organization_id
     * @param $classification_id
     * @param $threshold
     *
     * @return array La lista de productos.
     */
    public function getProductsWithClassificationThreshold($organization_id, $classification_id, $threshold)
    {
        $query = $this->createQueryBuilder('p');
        $query
            ->select('p as product')
            ->addSelect('SUM(i.value) as _threshold')
            ->innerJoin('p.organization', 'o')
            ->innerJoin('p.indicators', 'i')
            ->leftJoin('i.criterion', 'c')
            ->leftJoin('c.classification', 'cl')
            ->leftJoin('p.certificates', 'r')
            ->where($query->expr()->eq('o.id', $organization_id))
            ->andWhere($query->expr()->eq('cl.id', $classification_id))
            ->groupBy('p.id')
            ->having($query->expr()->gte('_threshold', $threshold))
        ;

        $result= $query
            ->getQuery()
            ->getResult()
        ;

        return array_column($result, "product");
    }
} 