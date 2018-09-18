<?php

namespace AppBundle\Tests\Unit;


use AppBundle\Service\DatabaseService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use AppBundle\Entity\Collection;


class CollectionTest extends WebTestCase
{

    public function testAddCustomers() {
        $customers = [
            ['name' => 'Leandro', 'age' => 26],
            ['name' => 'Marcelo', 'age' => 30],
            ['name' => 'Alex', 'age' => 18],
        ];

        self::bootKernel();

        // returns the real and unchanged service container
        $container = self::$kernel->getContainer();

        $collection = new Collection($container->get('database_service'), $container->get('cache_service'));

        $collection->addCustomers( $customers );

        $this->assertTrue($collection->addCustomers( $customers ));

    }

    public function testDeleteCustomers()
    {

        self::bootKernel();

        // returns the real and unchanged service container
        $container = self::$kernel->getContainer();

        $collection = new Collection($container->get('database_service'), $container->get('cache_service'));

        $customers = [
            ['name' => 'Leandro', 'age' => 26],
            ['name' => 'Marcelo', 'age' => 30],
            ['name' => 'Alex', 'age' => 18],
        ];
        $collection->addCustomers( $customers );
        $collection->loadCustomers();

        $collection->deleteCustomers();
        $customers = $collection->getCustomers();

        $this->assertTrue(count($customers) === 0);

    }

    public function testLoadCustomersInCache()
    {

        self::bootKernel();

        // returns the real and unchanged service container
        $container = self::$kernel->getContainer();

        $collection = new Collection($container->get('database_service'), $container->get('cache_service'));
        $collection->deleteCustomers();

        $customers = [
            ['name' => 'Leandro', 'age' => 26],
            ['name' => 'Marcelo', 'age' => 30],
            ['name' => 'Alex', 'age' => 18],
        ];
        $collection->addCustomers( $customers );
        $customers = $collection->getCustomers();
        $cache = $collection->getCustomersCache();

        $this->assertEquals(count($customers), count($cache));

    }

    public function testGetCacheIfDBProblems()
    {
        self::bootKernel();

        // returns the real and unchanged service container
        $container = self::$kernel->getContainer();

        $collection = new Collection($container->get('database_service'), $container->get('cache_service'));
        $collection->deleteCustomers();
        echo '----------------Z'.$collection->getChangesLog().'----------------C';
        $customers = [
            ['name' => 'Leandro', 'age' => 26],
            ['name' => 'Marcelo', 'age' => 30],
            ['name' => 'Alex', 'age' => 18],
        ];
        $collection->addCustomers( $customers );
        $customers1 = $collection->getCustomers();

        $customers = [
            ['name' => 'Uno', 'age' => 1],
            ['name' => 'Dos', 'age' => 2],
            ['name' => 'Tres', 'age' => 3],
        ];

        $collection->addCustomers( $customers );
        $customers2 = $collection->getCustomers();

        $otherDatabase = new DatabaseService('localhost', 999,'test');

        $newCollection = new Collection($otherDatabase, $container->get('cache_service'));
        $ncustomers = $newCollection->getCustomers();

        $this->assertTrue(isset($ncustomers['error'])); // There is a error

        $this->assertEquals(count($customers1), count($ncustomers['customers']));   // The total records with error are the same to the total before error


    }

}