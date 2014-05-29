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

    namespace PHY\Model;

    use PHY\Database\Mysqli\Manager;
    use PHY\Model\User;
    use PHY\Database\TestDatabase;

    /**
     * Test our User model.
     *
     * @packagePHY\Tests\Model\UserTest
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class UserTest extends \PHPUnit_Framework_TestCase
    {

        public static function testDatabase()
        {
            return new TestDatabase;
        }

        public function setUp()
        {
        }

    }