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

    namespace PHY\Model;

    /**
     * Grab collections of our precious models.
     *
     * @package PHY\Model\Collection
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Collection implements \PHY\Manager\ICollection, \Iterator
    {

        protected $items = null;
        protected $manager = null;
        protected $raw = false;
        protected static $_source = '\PHY\Model\Entity';

        /**
         * Set our manager used for our given collection.
         * 
         * @param \PHY\Manager\IManager $manager
         * @return \PHY\Model\Collection
         */
        public function setManager(\PHY\Manager\IManager $manager)
        {
            $this->manager = $manager;
            return $this;
        }

        /**
         * Get our defined manager.
         * 
         * @return \PHY\Model\IManager
         */
        public function getManager()
        {
            return $this->manager;
        }

        /**
         * Initialize our Query if one hasn't already been set.
         *
         * @return \PHY\Model\Collection
         */
        public function getQuery()
        {
            if ($this->query === null) {
                $this->query = $this->getManager()->createQuery();
            }
            return $this->query;
        }

        /**
         * Return an entity
         *
         * @return \PHY\Model\Entity
         */
        public function current()
        {
            $current = current($this->items);
            if ($this->raw) {
                return $current;
            } else {
                $class = self::$_source;
                return new $class($current);
            }
        }

        /**
         * Move our collection pointer forward once.
         */
        public function next()
        {
            next($this->items);
        }

        /**
         * {@inheritDoc}
         */
        public function from()
        {
            return $this->getQuery()->from;
        }

        /**
         * Load our actual collection and populate self::$items with the grabbed
         * data.
         */
        public function load()
        {
            if ($this->items === null) {
                $this->items = $this->getQuery()->execute()->getIterator();
            }
        }

        /**
         * {@inheritDoc}
         */
        public function key()
        {
            return key($this->items);
        }

        /**
         * {@inheritDoc}
         */
        public function map(\PHY\Manager\callable $function)
        {
            $this->load();
            return array_map($function, $this->items);
        }

        /**
         * {@inheritDoc}
         */
        public function order()
        {
            return $this->getQuery()->order;
        }

        /**
         * {@inheritDoc}
         */
        public function raw($raw = false)
        {
            $this->raw = $raw;
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function reduce(\callable $function, $default = [])
        {
            return array_reduce($this->items, $function, $default);
        }

        /**
         * Reset our pointer to the first item on our collection.
         */
        public function rewind()
        {
            $this->load();
            reset($this->items);
        }

        /**
         * {@inheritDoc}
         */
        public function select()
        {
            return $this->getQuery()->select;
        }

        /**
         * Return true if this current pointer exists.
         *
         * @return boolean
         */
        public function valid()
        {
            return key($this->items) !== null;
        }

        /**
         * {@inheritDoc}
         */
        public function where()
        {
            return $this->getQuery()->where;
        }

    }
