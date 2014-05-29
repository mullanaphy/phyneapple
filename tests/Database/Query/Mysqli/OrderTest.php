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

    use PHY\Database\Mysqli\Query\Order;

    /**
     * Test our Mysqli Order class.
     *
     * @packagePHY\Tests\Database\Mysqli\Query\OrderTest
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class OrderTest extends \PHPUnit_Framework_TestCase
    {

        public function testDefaultKeyAndDirection()
        {
            $order = new Order;
            $this->assertEquals(" ORDER BY `id` ASC ", $order->toString());
        }

        public function testDefaultDirection()
        {
            $order = new Order;
            $order->by('name');
            $this->assertEquals(" ORDER BY `name` ASC ", $order->toString());
        }

        public function testKeyAndDirection()
        {
            $order = new Order;
            $order->by('name', 'desc');
            $this->assertEquals(" ORDER BY `name` DESC", $order->toString());
        }

        public function testMalformedDirection()
        {
            $order = new Order;
            $order->by('name', 'banana');
            $this->assertEquals(" ORDER BY `name` ASC ", $order->toString());
        }

        public function testAliasedOrder()
        {
            $order = new Order;
            $order->by(['p' => 'primary']);
            $this->assertEquals(" ORDER BY p.`primary` ASC ", $this->toString());
        }

        public function testMultipleOrders()
        {
            $order = new Order;
            $order->by(['p' => 'name'], 'ASC');
            $order->by(['s' => 'email'], 'DESC');
            $this->assertEquals(" ORDER BY p.`primary` ASC, s.`email` DESC ", $this->toString());
        }

        public function testToArray()
        {
            $order = new Order;
            $order->by(['p' => 'name'], 'ASC');
            $order->by(['s' => 'email'], 'DESC');
            $order->by(['t' => 'created'], 'DESC');
            $order->by('saiyan_level', 'DESC');
            $this->assertEquals([
                "p.`name` ASC",
                "s.`email` DESC",
                "t.`created` DESC",
                "`saiyan_level` DESC",
            ], $order->toArray());
        }

        public function testToString()
        {
            $order = new Order;
            $order->by(['p' => 'name'], 'ASC');
            $order->by(['s' => 'email'], 'DESC');
            $order->by(['t' => 'created'], 'DESC');
            $order->by('saiyan_level', 'DESC');
            $this->assertEquals(" ORDER BY p.`name` ASC, s.`email` DESC, t.`created` DESC, `saiyan_level` DESC ", $order->toString());
            $this->assertEquals((string)$order, $order->toString());
        }

        public function testToJson()
        {
            $order = new Order;
            $order->by(['p' => 'name'], 'ASC');
            $order->by(['s' => 'email'], 'DESC');
            $order->by(['t' => 'created'], 'DESC');
            $order->by('saiyan_level', 'DESC');
            $this->assertEquals(json_encode([
                "p.`name` ASC",
                "s.`email` DESC",
                "t.`created` DESC",
                "`saiyan_level` DESC",
            ], JSON_PRETTY_PRINT), $order->toJson(JSON_PRETTY_PRINT));
        }

    }