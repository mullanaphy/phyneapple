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

    use PHY\Database\Mysqli\Query\Where;

    /**
     * Test our Mysqli Where class.
     *
     * @packagePHY\Tests\Database\Mysqli\Query\WhereTest
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class WhereTest extends \PHPUnit_Framework_TestCase
    {

        public function testCleanInt()
        {
            $where = new Where;
            $this->assertEquals(2, $where->clean(2));
        }

        public function testCleanBool()
        {
            $where = new Where;
            $this->assertEquals(0, $where->clean(false));
        }

        public function testCleanFloat()
        {
            $where = new Where;
            $this->assertEquals(2.0, $where->clean(2.0));
        }

        public function testCleanString()
        {
            $where = new Where;
            $this->assertEquals("'three'", $where->clean('three'));
        }

        public function testAlias()
        {
            $where = new Where;
            $where->field('test', 'a')->is('banana');
            $this->assertEquals(" WHERE (`a`.`test` = 'banana') ", $where->toString());
        }

        public function testIn()
        {
            $where = new Where;
            $where->field('test')->in([1, 2.0, 'three']);
            $this->assertEquals(" WHERE (`test` IN (1,2.0,'three')) ", $where->toString());
        }

        public function testNotIn()
        {
            $where = new Where;
            $where->field('test')->in([1, 2.0, 'three']);
            $this->assertEquals(" WHERE (`test` NOT IN (1,2.0,'three')) ", $where->toString());
        }

        public function testIs()
        {
            $where = new Where;
            $where->field('test')->is('banana');
            $this->assertEquals(" WHERE (`test` = 'banana') ", $where->toString());
        }

        public function testGt()
        {
            $where = new Where;
            $where->field('test')->gt(1);
            $this->assertEquals(" WHERE (`test` > 1) ", $where->toString());
        }

        public function testGte()
        {
            $where = new Where;
            $where->field('test')->gte(1);
            $this->assertEquals(" WHERE (`test` >= 1) ", $where->toString());
        }

        public function testLt()
        {
            $where = new Where;
            $where->field('test')->lt(1);
            $this->assertEquals(" WHERE (`test` < 1) ", $where->toString());
        }

        public function testLte()
        {
            $where = new Where;
            $where->field('test')->lte(1);
            $this->assertEquals(" WHERE (`test` <= 1) ", $where->toString());
        }

        public function testRange()
        {
            $where = new Where;
            $where->field('test')->range(1, 3);
            $this->assertEquals(" WHERE (`test` BETWEEN 1 AND 3) ", $where->toString());
        }

        public function testLike()
        {
            $where = new Where;
            $where->field('test')->like('banana%');
            $this->assertEquals(" WHERE (`test` LIKE 'banana%') ", $where->toString());
        }

        public function testNotLike()
        {
            $where = new Where;
            $where->field('test')->notLike('banana%');
            $this->assertEquals(" WHERE (`test` NOT LIKE 'banana%') ", $where->toString());
        }

        public function testNot()
        {
            $where = new Where;
            $where->field('test')->not('banana');
            $this->assertEquals(" WHERE (`test` != 'banana') ", $where->toString());
        }

        public function testAlso()
        {
            $where = new Where;
            $where->field('test')->is('banana')->also('also')->is('true');
            $this->assertEquals(" WHERE (`test` = 'banana' AND `also` = 'true') ", $where->toString());
        }

        public function testAlsoAlias()
        {
            $where = new Where;
            $where->field('test')->is('banana')->also('also', 'a')->is('true');
            $this->assertEquals(" WHERE (`test` = 'banana' AND `a`.`also` = 'true') ", $where->toString());
        }

        public function testInstead()
        {
            $where = new Where;
            $where->field('test')->is('banana')->instead('instead')->is('true');
            $this->assertEquals(" WHERE (`test` = 'banana' OR `instead` = 'true') ", $where->toString());
        }

        public function testInsteadAlias()
        {
            $where = new Where;
            $where->field('test')->is('banana')->instead('instead', 'a')->is('true');
            $this->assertEquals(" WHERE (`test` = 'banana' OR `a`.`instead` = 'true') ", $where->toString());
        }

        public function testMultiple()
        {
            $where = new Where;
            $where->field('test')->is('banana')->field('secondary')->is('pineapple');
            $this->assertEquals(" WHERE (`test` = 'banana') AND (`secondary` = 'pineapple') ", $where->toString());
        }

        public function testToArray()
        {
            $where = new Where;
            $where->field('test')->is('banana');
            $where->field('other')->is('hey');
            $this->assertEquals([
                "(`test` = 'banana')",
                "(`other` = 'hey')"
            ], $where->toArray());
        }

        public function testToString()
        {
            $where = new Where;
            $where->field('test')->is('banana');
            $where->field('other')->is('hey');
            $this->assertEquals(" WHERE (`test` = 'banana') AND (`other` = 'hey') ", $where->toString());
            $this->assertEquals((string)$where, $where->toString());
        }

        public function testToJson()
        {
            $where = new Where;
            $where->field('test')->is('banana');
            $where->field('other')->is('hey');
            $this->assertEquals(json_encode([
                "(`test` = 'banana')",
                "(`other` = 'hey')"
            ], JSON_PRETTY_PRINT), $where->toJson(JSON_PRETTY_PRINT));
        }

    }