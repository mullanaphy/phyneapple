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
     * Our Where classes should all have the same query building functions.
     *
     * @package PHY\Database\IWhere
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    interface IWhere extends \PHY\Database\Query\IElement
    {

        /**
         * Set a field to compare against.
         *
         * @param string $field
         * @return \PHY\Database\Query\IWhere
         */
        public function field($field);

        /**
         * Check for results where the last $field is in $value.
         *
         * @param array $value
         * @return \PHY\Database\Query\IWhere
         */
        public function in(array $value);

        /**
         * Check for results where the last $field is equal to $value.
         *
         * @param mixed $value
         * @return \PHY\Database\Query\IWhere
         */
        public function is($value);

        /**
         * Check for results where the last $field is like $value.
         * 
         * @param string $value
         * @return \PHY\Database\Query\IWhere
         */
        public function like($value);

        /**
         * Check for results where the last $field isn't $value.
         *
         * @param mixed $value
         * @return \PHY\Database\Query\IWhere
         */
        public function not($value);

        /**
         * For for results where the last $field isn't in $value.
         *
         * @param array $value
         * @return \PHY\Database\Query\IWhere
         */
        public function notIn(array $value);

        /**
         * Check for results where the last $field isn't like $value.
         *
         * @param string $value
         * @return \PHY\Database\Query\IWhere
         */
        public function notLike($value);

        /**
         * AND join the $field and the last set $field.
         *
         * @param string $field
         * @return \PHY\Database\Query\IWhere
         */
        public function also($field);

        /**
         * OR join the $field and the last set $field.
         *
         * @param string $field
         * @return \PHY\Database\Query\IWhere
         */
        public function instead($field);

        /**
         * See if we can find results where $field is within $start and $finish.
         *
         * @param mixed $start
         * @param mixed $finish
         */
        public function range($start, $finish);

        /**
         * See if we can get results where $field is less than $value.
         *
         * @param mixed $value
         * @return \PHY\Database\Query\IWhere
         */
        public function lt($value);

        /**
         * See if we can get results where $field is greater than $value.
         *
         * @param mixed $value
         * @return \PHY\Database\Query\IWhere
         */
        public function gt($value);

        /**
         * See if we can get results where $field is less than or equal to
         * $value.
         *
         * @param mixed $value
         * @return \PHY\Database\Query\IWhere
         */
        public function lte($value);

        /**
         * See if we can get results where $field is greater than or equal to
         * $value.
         *
         * @param mixed $value
         * @return \PHY\Database\Query\IWhere
         */
        public function gte($value);
    }
