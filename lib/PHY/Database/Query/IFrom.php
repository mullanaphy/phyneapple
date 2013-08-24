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

    namespace PHY\Database\Query;

    /**
     * Our From classes should all have the same query building functions.
     *
     * @package PHY\Database\Query\IFrom
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    interface IFrom extends \PHY\Database\Query\IElement
    {

        /**
         * Initial table to grab from.
         *
         * @param string $table
         * @param string $alias
         * @return \PHY\Database\Query\IFrom
         */
        public function from($table = '', $alias = false);

        /**
         * Left Join a second table, $alias is a $key => $value mapping, $key is
         * the table on the left to join against with table $value. Mapping is
         * a $key => $value mapping of columns to match on the left table $key
         * and the table being joined $value.
         *
         * @param string $table
         * @param mixed $alias
         * @param array $mapping
         * @return \PHY\Database\Query\IFrom
         */
        public function leftJoin($table = '', $alias = false, array $mapping = []);

        /**
         * Left Join a second table, $alias is a $key => $value mapping, $key is
         * the table on the right to join against with table $value. Mapping is
         * a $key => $value mapping of columns to match on the right table $key
         * and the table being joined $value.
         *
         * @param string $table
         * @param mixed $alias
         * @param array $mapping
         * @return \PHY\Database\Query\IFrom
         */
        public function rightJoin($table = '', $alias = false, array $mapping = []);

        /**
         * Left Join a second table, $alias is a $key => $value mapping, $key is
         * the table on the inside to join against with table $value. Mapping is
         * a $key => $value mapping of columns to match on the inside table $key
         * and the table being joined $value.
         *
         * @param string $table
         * @param mixed $alias
         * @param array $mapping
         * @return \PHY\Database\Query\IFrom
         */
        public function innerJoin($table = '', $alias = false, array $mapping = []);

        /**
         * Left Join a second table, $alias is a $key => $value mapping, $key is
         * the table on the outside to join against with table $value. Mapping is
         * a $key => $value mapping of columns to match on the outside table $key
         * and the table being joined $value.
         *
         * @param string $table
         * @param mixed $alias
         * @param array $mapping
         * @return \PHY\Database\Query\IFrom
         */
        public function outerJoin($table = '', $alias = false, array $mapping = []);

        /**
         * Alias of leftJoin.
         *
         * @param string $type
         * @param string $table
         * @param mixed $alias
         * @param array $mapping
         * @return \PHY\Database\Query\IFrom
         */
        public function join($type = 'left', $table = '', $alias = false, array $mapping = []);
    }
