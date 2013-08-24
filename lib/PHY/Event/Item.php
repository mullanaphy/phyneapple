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

    namespace PHY\Event;

    /**
     * Our actual event item that gets pushed along.
     *
     * @package PHY\Event\Item
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Item
    {

        protected $name = 'event';
        protected $values = [];
        protected $dispatcher;
        protected $time = 0;
        protected $children = 0;
        protected $triggered = 0;

        /**
         * Inject our event's name and the values to pass along.
         *
         * @param string $name
         * @param array $values
         */
        public function __construct($name = 'event', array $values = [])
        {
            $this->setName($name);
            $this->setValues($values);
        }

        /**
         * Get a defined value.
         *
         * @param scalar $key
         * @return mixed
         */
        public function __get($key)
        {
            if (array_key_exists($key, $this->values)) {
                return $this->values[$key];
            }
        }

        /**
         * Set our event's name.
         *
         * @param string $name
         * @return \PHY\Event\Item
         */
        public function setName($name = 'event')
        {
            $this->name = $name;
            return $this;
        }

        /**
         * Get our event's name.
         *
         * @return string
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * Set values.
         *
         * @param array $values
         * @return \PHY\Event\Item
         */
        public function setValues(array $values = [])
        {
            $this->values = $values;
            return $this;
        }

        /**
         * Get our event's values.
         *
         * @return array
         */
        public function getValues()
        {
            return $this->values;
        }

        /**
         * Set our dispatcher.
         *
         * @param \PHY\Event\Dispatcher $dispatcher
         * @return \PHY\Event\Item
         */
        public function setDispatcher(\PHY\Event\Dispatcher $dispatcher)
        {
            $this->dispatcher = $dispatcher;
            return $this;
        }

        /**
         * Get our assigned dispatcher.
         *
         * @return \PHY\Event\Dispatcher
         */
        public function getDispatcher()
        {
            return $this->dispatcher;
        }

        /**
         * Set our event's time.
         *
         * @param int $time
         * @return \PHY\Event\Item
         */
        public function setTime($time = 0)
        {
            $this->time = $time;
            return $this;
        }

        /**
         * Get our event's time.
         *
         * @return int
         */
        public function getTime()
        {
            return $this->time;
        }

        /**
         * Increase our triggered events counter.
         *
         * @return \PHY\Event\Item
         */
        public function trigger()
        {
            ++$this->triggered;
            return $this;
        }

        /**
         * Get our triggered events.
         *
         * @return int
         */
        public function getTriggered()
        {
            return $this->triggered;
        }

        /**
         * Set our child events.
         *
         * @param int $children
         * @return \PHY\Event\Item
         */
        public function setChildren($children = 0)
        {
            $this->children = $children;
            return $this;
        }

        /**
         * Get our child events.
         *
         * @return int
         */
        public function getChildren()
        {
            return $this->children;
        }

    }
