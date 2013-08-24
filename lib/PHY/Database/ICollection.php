<?php

    /**
     * Phyneapple!
     *
     * LICENSE
     *
     * This source file is subject to the Open Software License (OSL 3.0)
     * that is bundled with this package in the file LICENSE.txt.
     * It is also available through the world-wide-web at this URL:
     * http://opensource.org/licenses/osl-3.0.php
     * If you did not receive a copy of the license and are unable to
     * obtain it through the world-wide-web, please send an email
     * to license@phyneapple.com so we can send you a copy immediately.
     *
     */

    namespace PHY\Database;

    /**
     * Contract for all Database Collections.
     *
     * @package PHY\Database\ICollection
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    interface ICollection extends \Iterator
    {

        /**
         * Return a Where object.
         *
         * @return \PHY\Model\DataMapper\Query\IWhere
         */
        public function where();

        /**
         * Map function to a collection, and get back the results.
         * 
         * @param callable $function
         * @return array
         */
        public function map(\callable $function);

        /**
         * Set whether each row returned is a mapped entity or just pure
         * database results.
         * 
         * @param boolean $raw
         * @return \PHY\Database\ICollection
         */
        public function raw($raw = false);

        /**
         * Reduce a collection to a single value.
         *
         * @param callable $function
         * @param mixed $default
         * @return mixed
         */
        public function reduce(\callable $function, $default = []);

        /**
         * Return a From object.
         *
         * @return \PHY\Model\Query\IFrom
         */
        public function from();

        /**
         * Return an Order object.
         *
         * @return \PHY\Model\Query\IOrder
         */
        public function order();

        /**
         * Return a Select object.
         *
         * @return \PHY\Model\Query\ISelect
         */
        public function select();
    }
