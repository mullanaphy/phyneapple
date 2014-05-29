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

    use PHY\Database\Mysqli\Query;
    use \PHY\Model\TestModel;

    /**
     * Test our Mysqli Query class.
     *
     * @packagePHY\Tests\Database\Mysqli\Query\QueryTest
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class QueryTest extends \PHPUnit_Framework_TestCase
    {

        public function testUpsert()
        {
            $query = new Query(new TestModel);
            $query->upsert()->field('id')->set(1)->field('key')->set('value')->field('other')->set('also');
            $this->assertEqual("INSERT `primary` p (`id`, `key`, `other`) VALUES (1, 'value', 'also') ON DUPLICATE KEY SET p.`key`='value', p.`other`='also'", $query->toString());
        }

        public function testInsert()
        {
            $query = new Query(new TestModel);
            $query->insert()->field('key')->set('value')->field('other')->set('also');
            $this->assertEqual("INSERT `primary` p (`key`, `other`) VALUES ('value', 'also') ", $query->toString());
        }

        public function testUpdate()
        {
            $query = new Query(new TestModel);
            $query->update()->field('id')->is(1)->field('key')->set('value')->field('other')->set('also');
            $this->assertEqual("UPDATE `primary` p SET p.`key`='value', p.`other`='also' WHERE p.`id`=1");
        }

        public function testSelect()
        {
            $query = new Query(new TestModel);
            $query->select('key')->field('id')->is(1);
            $this->assertEqual("SELECT p.`key` FROM `primary` p");
        }

    }