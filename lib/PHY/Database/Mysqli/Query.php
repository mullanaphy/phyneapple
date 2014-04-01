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

    use PHY\Database\IQuery;
    use PHY\Database\IManager;
    use PHY\Model\IEntity;

    /**
     * Our main Query element. This is in essence our query builder.
     *
     * @package PHY\Database\Mysqli\Query
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Query extends Query\Element implements IQuery
    {

        private $elements = [];
        private $results = null;
        private $string = '';

        /**
         * {@inheritDoc}
         */
        public function __construct(IManager $manager = null, IEntity $model = null)
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
         * @param IEntity $model
         * @return $this
         */
        public function selectFromModel(IEntity $model)
        {
            $this->setModel($model);
            /* @var Query\From $from */
            $from = $this->get('from');
            /* @var Query\Select $select */
            $select = $this->get('select');
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
                    throw new Exception('"' . $object . '" is not an object... I am blaming you...');
                }
            } else {
                throw new Exception('"' . $object . '" is undefined. Available calls are "' . implode('", "', $this->elements) . '".');
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
                'select' => new Query\Select,
                'from' => new Query\From,
                'where' => new Query\Where,
                'having' => new Query\Having,
                'order' => new Query\Order
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
        public function setManager(IManager $manager)
        {
            parent::setManager($manager);
            foreach ($this->elements as $element) {
                /* @var Query\Element $element */
                $element->setManager($manager);
            }
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function setModel(IEntity $model)
        {
            parent::setModel($model);
            foreach ($this->elements as $element) {
                /* @var Query\Element $element */
                $element->setModel($model);
            }
            return $this;
        }

    }
