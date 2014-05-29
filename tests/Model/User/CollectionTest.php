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

    namespace PHY\Tests\Model\User;

    use PHY\Model\User\Collection;

    /**
     * Test our User collection.
     *
     * @packagePHY\Tests\Model\User\CollectionTest
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class CollectionTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * See if we correctly get back an User model.
         *
         * @see \PHY\Model\User\Collection::current();
         */
        public function testCurrent()
        {
            $collection = new Collection;
            $this->assertInstanceof('\PHY\Model\User', $collection->getFirstItem());
        }

    }