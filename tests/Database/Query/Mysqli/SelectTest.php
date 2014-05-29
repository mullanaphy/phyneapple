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

    use PHY\Database\Mysqli\Query\Select;

    /**
     * Test our Mysqli Select class.
     *
     * @packagePHY\Tests\Database\Mysqli\Query\SelectTest
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class SelectTest extends \PHPUnit_Framework_TestCase
    {

        public function testCountStatement()
        {
            $select = new Select;
            $this->assertEquals('COUNT(`id`)', $select->count('id')->toString());
        }

        public function testCountStatementAlias()
        {
            $select = new Select;
            $this->assertEquals('COUNT(`a`.`id`)', $select->count('id', 'a')->toString());
        }

        public function testFieldStatement()
        {
            $select = new Select;
            $this->assertEquals('`id`', $select->field('id')->toString());
        }

        public function testFieldStatementAlias()
        {
            $select = new Select;
            $this->assertEquals(' SELECT `a`.`id` ', $select->field('id', 'a')->toString());
        }

        public function testMaxStatement()
        {
            $select = new Select;
            $this->assertEquals(' SELECT MAX(`id`) ', $select->max('id')->toString());
        }

        public function testMaxStatementAlias()
        {
            $select = new Select;
            $this->assertEquals(' SELECT MAX(`a`.`id`) ', $select->max('id', 'a')->toString());
        }

        public function testMinStatement()
        {
            $select = new Select;
            $this->assertEquals(' SELECT MIN(`id`) ', $select->min('id')->toString());
        }

        public function testMinStatementAlias()
        {
            $select = new Select;
            $this->assertEquals(' SELECT MIN(`a`.`id`) ', $select->min('id', 'a')->toString());
        }

        public function testRawStatement()
        {
            $select = new Select;
            $this->assertEquals(' SELECT BANANA RAMA ', $select->raw(' BANANA RAMA ')->toString());
        }

        public function testToArray()
        {
            $select = new Select;
            $select->field('id');
            $select->field('name');
            $select->count('*', 'posts');
            $this->assertEquals([
                '`id`',
                '`name`',
                'COUNT(`posts`.*)'
            ], $select->toArray());
        }

        public function testToString()
        {
            $select = new Select;
            $select->field('id');
            $select->field('name');
            $select->count('*', 'posts');
            $this->assertEquals(' SELECT `id`, `name`, COUNT(`posts`.*) ', $select->toString());
            $this->assertEquals((string)$select, $select->toString());
        }

        public function testToJson()
        {
            $select = new Select;
            $select->field('id');
            $select->field('name');
            $select->count('*', 'posts');
            $this->assertEquals(json_encode([
                '`id`',
                '`name`',
                'COUNT(`posts`.*)'
            ], JSON_PRETTY_PRINT), $select->toJson(JSON_PRETTY_PRINT));
        }

    }