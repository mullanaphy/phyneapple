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

    use PHY\Database\Mysqli\Query\From;

    /**
     * Test our MySQLi From class.
     *
     * @packagePHY\Tests\Database\Mysqli\Query\FromTest
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class FromTest extends \PHPUnit_Framework_TestCase
    {

        public function testFrom()
        {
            $from = new From;
            $from->from('Primary');
            $this->assertEquals(' FROM `Primary` ', $from->toString());
        }

        public function testFromAlias()
        {
            $from = new From;
            $from->from('Primary', 'p');
            $this->assertEquals(' FROM `Primary` p ', $from->toString());
        }

        public function testFromLeftJoinMapping()
        {
            $from = new From;
            $from->from('Primary', 'p');
            $from->leftJoin('Secondary', 's', ['primary_id' => 'id']);
            $this->assertEquals(' FROM `Primary` p LEFT JOIN `Secondary` ON (p.`id` = `Secondary`.`primary_id`)', $from->toString());
        }

        public function testFromLeftJoinComplex()
        {
            $from = new From;
            $from->from('Primary', 'p');
            $from->leftJoin('Secondary', 's', [
                ['primary_id' => 'id'],
                ['secondary_other' => 'other']
            ], ['secondary_and' => 'and']);
            $this->assertEquals(' FROM `Primary` p LEFT JOIN `Secondary` s ON ((p.`id` = s.`primary_id` OR p.`other` = s.`secondary_other`) AND (p.`and` = s.`secondary_and`) ', $from->toString());
        }

        public function testFromLeftJoinString()
        {
            $from = new From;
            $from->from('Primary', 'p');
            $from->leftJoin('Secondary', 's', 'p.`id` = s.`primary_id`');
            $this->assertEquals(' FROM `Primary` p LEFT JOIN `Secondary` s ON (p.`id` = s.`primary_id`) ', $from->toString());
        }

        public function testFromLeftJoinMultiple()
        {
            $from = new From;
            $from->from('Primary', 'p');
            $from->leftJoin('Secondary', 's', ['primary_id' => 'id']);
            $from->leftJoin('Tertiary', 't', ['primary_id' => 'id']);
            $this->assertEquals(' FROM `Primary` p LEFT JOIN `Secondary` s ON (p.`id` = s.`primary_id`) LEFT JOIN `Tertiary` t ON (p.`id` = t.`primary_id`) ', $from->toString());
        }

        public function testFromLeftJoinMultipleMappedToSecondary()
        {
            $from = new From;
            $from->from('Primary', 'p');
            $from->leftJoin('Secondary', 's', ['primary_id' => 'id']);
            $from->leftJoin('Tertiary', ['t' => 's'], ['primary_id' => 'id']);
            $this->assertEquals(' FROM `Primary` p LEFT JOIN `Secondary` s ON (p.`id` = s.`primary_id`) LEFT JOIN `Tertiary` t ON (p.`id` = t.`primary_id`) ', $from->toString());
        }

        public function testRawFrom()
        {
            $from = new From;
            $this->assertEquals(' FROM BANANA RAMA ', $from->raw(' BANANA RAMA '));
        }

        public function testToArray()
        {
            $from = new From;
            $from->from('Primary', 'p');
            $from->leftJoin('Secondary', 's', ['primary_id' => 'id']);
            $from->leftJoin('Tertiary', ['t' => 's'], ['primary_id' => 'id']);
            $this->assertEquals([
                'p' => [
                    'table' => '`Primary`',
                    'alias' => 'p',
                    'type' => '',
                    'on' => ''
                ],
                's' => [
                    'table' => '`Secondary`',
                    'alias' => 's',
                    'type' => 'left',
                    'on' => 'p.`id` = s.`primary_id`'
                ],
                't' => [
                    'table' => '`Tertiary`',
                    'alias' => 't',
                    'type' => 'left',
                    'on' => 'p.`id` = t.`primary_id`'
                ]
            ], $from->toArray());
        }

        public function testToString()
        {
            $from = new From;
            $from->from('Primary', 'p');
            $from->leftJoin('Secondary', 's', ['primary_id' => 'id']);
            $from->leftJoin('Tertiary', 't', ['primary_id' => 'id']);
            $this->assertEquals(' FROM `Primary` p LEFT JOIN `Secondary` s ON (p.`id` = s.`primary_id`) LEFT JOIN `Tertiary` t ON (p.`id` = t.`primary_id`) ', $from->toString());
        }

        public function testToJson()
        {
            $from = new From;
            $from->from('Primary', 'p');
            $from->leftJoin('Secondary', 's', ['primary_id' => 'id']);
            $from->leftJoin('Tertiary', ['t' => 's'], ['primary_id' => 'id']);
            $this->assertEquals(json_encode([
                'p' => [
                    'table' => '`Primary`',
                    'alias' => 'p',
                    'type' => '',
                    'on' => ''
                ],
                's' => [
                    'table' => '`Secondary`',
                    'alias' => 's',
                    'type' => 'left',
                    'on' => 'p.`id` = s.`primary_id`'
                ],
                't' => [
                    'table' => '`Tertiary`',
                    'alias' => 't',
                    'type' => 'left',
                    'on' => 'p.`id` = t.`primary_id`'
                ]
            ], JSON_PRETTY_PRINT), $from->toJson(JSON_PRETTY_PRINT));
        }

    }