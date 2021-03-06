<?php
/**
 * This file is part of the lanzadera package.
 * 
 * (c) Sergio Gómez
 * 
 * For the full copyright and licence information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Faker\Factory as FakerFactory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class DataFixture extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * Container.
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Faker.
     *
     * @var Generator
     */
    protected $faker;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->faker = FakerFactory::create('es_ES');
        $this->faker->addProvider(new \AppBundle\Faker\Provider\Lanzadera($this->faker));
    }

    public function __call($method, $arguments)
    {
        $matches = array();
        if (preg_match('/^get(.*)Repository$/', $method, $matches)) {
            return $this->get('lanzadera.repository.'.$matches[1]);
        }

        return call_user_func_array(array($this, $method), $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Get service by id.
     *
     * @param string $id
     * @return object
     */
    public function get($id)
    {
        return $this->container->get($id);
    }

    protected function createImage($name, $type='cats')
    {
        $repo = $this->getMediaRepository();

        /** @var Media $image */
        $image = $repo->createNew();
        $image->setBinaryContent($this->faker->image('/tmp', 400, 400, $type));
        $image->setProviderName('sonata.media.provider.image');
        $image->setContext('default');
        $image->setName($name);

        return $image;
    }
} 