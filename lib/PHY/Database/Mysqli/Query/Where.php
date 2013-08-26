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

    namespace PHY\Database\Mysqli\Query;

    /**
     * Our Where classes should all have the same query building functions.
     *
     * @package PHY\Database\Mysqli\Query\Where
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Where extends \PHY\Database\Mysqli\Query\Element implements \PHY\Database\Query\IWhere
    {

        protected $where = [];
        protected $current = [];

        /**
         * {@inheritDoc}
         */
        public function also($field, $alias = false)
        {
            if (!$this->checkForValue()) {

            }
            $this->current[] = [
                'field' => $this->_clean($field),
                'alias' => $alias,
                'value' => null,
                'or' => false
            ];
        }

        /**
         * {@inheritDoc}
         */
        public function field($field, $alias = false)
        {
            if (!$this->checkForValue()) {

            }
            $this->current = [
                [
                    'field' => $this->clean($field),
                    'alias' => $alias,
                    'value' => null,
                    'or' => false
                ]
            ];
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function gt($value)
        {
            $this->throwExceptionForInproperChaining();
            $this->current[count($this->current) - 1]['value'] = ' > '.$this->clean($value);
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function gte($value)
        {
            $this->throwExceptionForInproperChaining();
            $this->current[count($this->current) - 1]['value'] = ' >= '.$this->clean($value);
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function in(array $value)
        {
            $this->throwExceptionForInproperChaining();
            $this->current[count($this->current) - 1]['value'] = ' IN ('.implode(',', array_map([$this, 'clean'], $value)).")";
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function notIn(array $value)
        {
            $this->throwExceptionForInproperChaining();
            $this->current[count($this->current) - 1]['value'] = ' NOT IN ('.implode(',', array_map([$this, 'clean'], $value)).")";
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function instead($field, $alias = false)
        {
            if (!$this->checkForValue()) {

            }
            $this->current[] = [
                'field' => $field,
                'alias' => $alias,
                'or' => true,
                'value' => null
            ];
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function is($value)
        {
            $this->throwExceptionForInproperChaining();
            $this->current[count($this->current) - 1]['value'] = ' = '.$this->clean($value);
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function like($value)
        {
            $this->throwExceptionForInproperChaining();
            $this->current[count($this->current) - 1]['value'] = ' LIKE '.$this->clean($value);
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function lt($value)
        {
            $this->throwExceptionForInproperChaining();
            $this->current[count($this->current) - 1]['value'] = ' < '.$this->clean($value);
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function lte($value)
        {
            $this->throwExceptionForInproperChaining();
            $this->current[count($this->current) - 1]['value'] = ' <= '.$this->clean($value);
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function not($value)
        {
            $this->throwExceptionForInproperChaining();
            $this->current[count($this->current) - 1]['value'] = ' != '.$this->clean($value);
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function notLike($value)
        {
            $this->throwExceptionForInproperChaining();
            $this->current[count($this->current) - 1]['value'] = ' NOT LIKE '.$this->clean($value);
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function range($start, $finish)
        {
            $this->throwExceptionForInproperChaining();
            $this->current[count($this->current) - 1]['value'] = ' BETWEEN('.$this->clean($start).','.$this->clean($finish).')';
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function toArray()
        {
            $complete = [];
            foreach ($this->where as $group) {
                $first = array_shift($group);
                $field = $first['alias']
                    ? '`'.$this->clean($first['alias']).'`.`'.$this->clean($first['field']).'`'
                    : '`'.$this->clean($first['field']).'`';
                $set = $field.$first['value'];
                foreach ($group as $part) {
                    $field = $part['alias']
                        ? '`'.$this->clean($part['alias']).'`.`'.$this->clean($part['field']).'`'
                        : '`'.$this->clean($part['field']).'`';
                    $set .= " ".($part['or']
                            ? 'OR'
                            : 'AND').' '.$field.$part['value'];
                }
                $complete[] = $set;
            }
            return $complete;
        }

        /**
         * {@inheritDoc}
         */
        public function toJSON($flags = 0)
        {
            return json_encode(['where' => $this->toArray()], $flags);
        }

        /**
         * {@inheritDoc}
         */
        public function toString()
        {
            if ($this->where) {
                return ' WHERE ('.implode(') AND (', $this->toArray()).') ';
            } else {
                return ' ';
            }
        }

        /**
         * {@inheritDoc}
         */
        protected function checkForField()
        {
            $current = last($this->current);
            return (bool)$current['field'];
        }

        /**
         * {@inheritDoc}
         */
        protected function checkForValue()
        {
            $current = end($this->current);
            return $current['value'] !== null;
        }

        /**
         * {@inheritDoc}
         */
        protected function throwExceptionForInproperChaining()
        {
            if (!$this->checkForField()) {
                throw new Exception('');
            } else if ($this->checkForValue()) {
                throw new Exception('');
            }
        }

        /**
         * {@inheritDoc}
         */
        public function raw($string)
        {
            $this->where[] = $string;
            return $this;
        }

    }
