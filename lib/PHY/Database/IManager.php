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
     * Contract for managers.
     *
     * @package PHY\Database\IManager
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    interface IManager
    {

        /**
         * Allow the ability to inject a database during initialization.
         *
         * @param \PHY\Database\IDatabase $database
         */
        public function __construct(\PHY\Database\IDatabase $database = null);

        /**
         * Inject our database object.
         * 
         * @param \PHY\Database\IDatabase $database
         * @return \PHY\Manager\IManager
         */
        public function setDatabase(\PHY\Database\IDatabase $database);

        /**
         * Get our database object.
         *
         * @return \PHY\Database\IDatabase $database
         */
        public function getDatabase();

        /**
         * Set a cache to use with our manager.
         * 
         * @param \PHY\Cache\ICache $cache
         * @return \PHY\Database\IManager
         */
        public function setCache(\PHY\Cache\ICache $cache);

        /**
         * Return our defined cache model for leveraging our load.
         *
         * @return \PHY\Cache\ICache
         */
        public function getCache();

        /**
         * Get a fresh model from our manager.
         *
         * @param string $model
         * @return \PHY\Database\IEntity $model
         */
        public function getModel($model);

        /**
         * Load a given model from our database and return a usable class.
         *
         * @param \PHY\Model\Entity
         * @return \PHY\Model\Entity
         */
        public function load($loadBy, \PHY\Model\Entity $model);

        /**
         * Save a model to our database.
         *
         * @param \PHY\Model\Entity $model
         * @return mixed
         */
        public function save(\PHY\Model\Entity $model);

        /**
         * Update an existing model.
         * 
         * @param \PHY\Model\Entity $model
         * @return boolean
         */
        public function update(\PHY\Model\Entity $model);

        /**
         * Insert a model into our database
         *
         * @param \PHY\Model\Entity $model
         * @return scalar
         */
        public function insert(\PHY\Model\Entity $model);

        /**
         * Delete a model from our database.
         * 
         * @param \PHY\Model\Entity $model
         * @return boolean
         */
        public function delete(\PHY\Model\Entity $model);

        /**
         * Return a query building object.
         *
         * @return \PHY\Database\IQuery
         */
        public function createQuery();

        /**
         * lean a string for database insertion.
         *
         * @param string
         * @return string
         */
        public function clean($string);
    }
