<?php

    /**
     * PHY
     *
     * LICENSE
     *
     * This source file is subject to the Open Software License (OSL 3.0)
     * that is bundled with this package in the file LICENSE.txt.
     * It is also available through the world-wide-web at this URL:
     * http://opensource.org/licenses/osl-3.0.php
     * If you did not receive a copy of the license and are unable to
     * obtain it through the world-wide-web, please send an email
     * to license@kinopio.net so we can send you a copy immediately.
     *
     */

    namespace PHY\Tests\Database\Mysqli\Query;

    use PHY\Database\Mysqli\Manager;
    use \PHY\Model\TestModel;
    use \PHY\Database\TestDatabase;

    /**
     * Test our Mysqli Manager class.
     *
     * @packagePHY\Tests\Database\Mysqli\Query\ManagerTest
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class ManagerTest extends \PHPUnit_Framework_TestCase
    {

        public function testSaveInsert()
        {
            $database = new TestDatabase;
            $model = new TestModel;
            $model->key = 'something';
            $model->value = 'bacon';
            $manager = new Manager($database);
            $this->assertEqual([
                'status' => 200,
                'response' => 1
            ], $manager->save($model));
            $this->assertEqual(1, $model->id);
        }

        public function testSaveUpdate()
        {
            $database = new TestDatabase;
            $model = new TestModel;
            $model->id = 1;
            $model->key = 'something';
            $model->value = 'bacon';
            $manager = new Manager($database);
            $this->assertEqual([
                'status' => 204
            ], $manager->save($model));
        }

        public function testRemove()
        {
            $database = new TestDatabase;
            $model = new TestModel;
            $model->id = 1;
            $model->key = 'something';
            $model->value = 'bacon';
            $manager = new Manager($database);
            $this->assertEqual([
                'status' => 204
            ], $manager->remove($model));
        }

        public function testFindById()
        {
            $database = new TestDatabase;
            $manager = new Manager($database);
            $model = $manager->find(1);
            $this->assertEqual('value', $model->key);
        }

        public function testFindOneBy()
        {
            $database = new TestDatabase;
            $manager = new Manager($database);
            $model = $manager->findOneBy([
                'key' => 'value'
            ]);
            $this->assertEqual('value', $model->key);
        }

        public function testFindBy()
        {
            $database = new TestDatabase;
            $manager = new Manager($database);
            $model = $manager->findBy([
                'key' => 'value'
            ]);
            $this->assertEqual(1, count($model));
        }

    }