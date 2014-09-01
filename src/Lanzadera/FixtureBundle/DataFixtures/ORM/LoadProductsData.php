<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 23/08/14
 * Time: 12:09
 */

namespace Lanzadera\FixtureBundle\DataFixtures\ORM;


use Doctrine\Common\Persistence\ObjectManager;
use Lanzadera\FixtureBundle\DataFixtures\DataFixture;
use Lanzadera\ProductBundle\Entity\Product;

class LoadProductsData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->createProduct("Revolución"));
        $manager->persist($this->createProduct("Solidaridad"));
        $manager->persist($this->createProduct("Justicia"));
        $manager->persist($this->createProduct("Voluntario"));
        $manager->persist($this->createProduct("Pobreza", array("Alta calidad y bajo precio", "No perecedero")));

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 10;
    }

    private function createProduct($name, $indicators = array())
    {
        $repo = $this->getProductRepository();

        /** @var Product $product */
        $product = $repo->createNew();

        $product->setName($this->faker->product);
        $product->setDescription($this->faker->text);
        $product->setCategory($this->getReference("Lanzadera.Category." . $this->faker->numberBetween(0,9)));
        $product->setOrganization($this->getReference("Lanzadera.Organization." . $this->faker->numberBetween(0, 4)));
        foreach($indicators as $indicator) {
            $product->addIndicator($this->getReference("Lanzadera.Indicator." . $indicator));
        }

        $this->setReference("Lanzadera.Product" . $name, $product);

        return $product;
    }
}