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

    namespace PHY\Database\Mysqli;

    /**
     * Our main Query element. This is in essence our query builder.
     *
     * @package PHY\Database\Mysqli\Query
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Query extends \PHY\Database\Mysqli\Query\Element implements \PHY\Database\IQuery
    {

        private $elements = [];
        private $results = null;
        private $string = '';

        /**
         * {@inheritDoc}
         */
        public function __construct(\PHY\Database\IManager $manager = null, \PHY\Model\Entity $model = null)
        {
            $this->elements = static::getElements();
            if ($manager !== null) {
                $this->setManager($manager);
            }
            if ($model !== null) {
                $this->setModel($model);
            }
        }

        /**
         * Create a SELECT query based on a model.
         * 
         * @param \PHY\Model\Entity $model
         * @return \PHY\Database\Mysqli\Query\
         */
        public function selectFromModel(\PHY\Model\Entity $model)
        {
            $this->setModel($model);
            $from = $this->from;
            $select = $this->select;
            $source = $model->getSource();
            foreach ($source['schema'] as $alias => $table) {
                if ($alias === 'primary') {
                    $from->from($table['table'], $alias);
                } else {
                    $from->leftJoin($table['table'], $alias, array_key_exists('mapping', $table)
                            ? $table['mapping']
                            : null);
                }
                $select->field('*', $alias);
            }

            return $this;
        }

        /**
         * Grab a portion of our query.
         *
         * @param string $object
         * @return \PHY\Database\Query\IElement
         * @throws Exception
         */
        public function get($object)
        {
            if (array_key_exists($object, $this->elements)) {
                if (is_object($this->elements[$object])) {
                    return $this->elements[$object];
                } else {
                    throw new Exception('"'.$object.'" is not an object... I am blaming you...');
                }
            } else {
                throw new Exception('"'.$object.'" is undefined. Available calls are "'.implode('", "', $this->elements).'".');
            }
        }

        /**
         * Return an initialized block element of our query.
         *
         * @param string $key
         * @return \PHY\Database\Query\IElement
         */
        public function __get($key)
        {
            return $this->get($key);
        }

        /**
         * {@inheritDoc}
         */
        public function toArray()
        {
            return $this->elements;
        }

        /**
         * {@inheritDoc}
         */
        public function toJSON($flags = 0)
        {
            return json_encode($this->elements, $flags);
        }

        /**
         * {@inheritDoc}
         */
        public function toString()
        {
            if (!$this->string) {
                $this->string = implode(' ', $this->elements);
            }
            return $this->string;
        }

        /**
         * Grab default elements.
         *
         * @return array
         */
        protected static function getElements()
        {
            return [
                'select' => new \PHY\Database\Mysqli\Query\Select,
                'from' => new \PHY\Database\Mysqli\Query\From,
                'where' => new \PHY\Database\Mysqli\Query\Where,
                'having' => new \PHY\Database\Mysqli\Query\Having,
                'order' => new \PHY\Database\Mysqli\Query\Order
            ];
        }

        /**
         * {@inheritDoc}
         */
        public function execute()
        {
            if ($this->results === null) {
                $this->results = $this->getManager()->getDatabase()->query($this->toString());
            }
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function getIterator()
        {
            $this->execute();
            return $this->results;
        }

        /**
         * {@inheritDoc}
         */
        public function setManager(\PHY\Database\IManager $manager)
        {
            parent::setManager($manager);
            foreach ($this->elements as $element) {
                $element->setManager($manager);
            }
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function setModel(\PHY\Model\Entity $model)
        {
            parent::setModel($model);
            foreach ($this->elements as $element) {
                $element->setModel($model);
            }
            return $this;
        }

    }

