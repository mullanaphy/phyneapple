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
     * Model contracts.
     *
     * @package PHY\Model\IEntity
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    interface IEntity
    {

        /**
         * Initiate the Item class.
         *
         * @param array $data
         */
        public function __construct(array $data = []);

        /**
         * Handle loadBy{$key}($value) calls.
         *
         * @param string $method
         * @param array $parameters
         * @return IEntity
         */
        public function __call($method, $parameters);

        /**
         * See if this initialize class exists.
         *
         * @return boolean
         */
        public function exists();

        /**
         * Set a key to it's corresponding value if it's allowed
         *
         * @param string $key
         * @param mixed $value
         * @return IEntity
         * @throws Exception
         */
        public function set($key = '', $value = '');

        /**
         * If key is set you'll get the value back. Otherwise NULL.
         *
         * @param string $key
         * @return mixed
         */
        public function get($key = '');

        /**
         * See if a resource exists.
         *
         * @param string $key
         * @return boolean
         */
        public function has($key);

        /**
         * Return an entity's collection.
         *
         * @return \PHY\Database\ICollection
         */
        public function getCollection();

        /**
         * See if a User doesn't exist or is deleted.
         *
         * @return boolean
         */
        public function isDeleted();

        /**
         * See if this instance is a new row in the Database or not.
         *
         * @return boolean
         */
        public function isNew();

        /**
         * See if this instance's data has been changed.
         *
         * @return boolean
         */
        public function isDifferent();

        /**
         * Get our model's id if it's set.
         *
         * @return string
         */
        public function id();

        /**
         * Get an array of settings.
         *
         * @return array
         */
        public function toArray();

        /**
         * Get an array of all changed values.
         *
         * @return array
         */
        public function getChanged();

        /**
         * Get a JSON string of data.
         *
         * @return string JSON encoded values
         */
        public function toJSON();

        /**
         * Get our entity's source (schema).
         *
         * @return array
         */
        public function getSource();
    }

