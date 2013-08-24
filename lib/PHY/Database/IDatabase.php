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
     * Database Interface to make sure all Database classes follow the rules.
     *
     * @package PHY\Database\IDatabase
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    interface IDatabase
    {

        /**
         * Extend this just so we can through out a 503 error if our Database is
         * acting flaky.
         *
         * @param string $host
         * @param string $username
         * @param string $password
         * @param string $table
         */
        public function __construct(array $settings = []);

        /**
         * Prepare a Query statement.
         *
         * @param string $sql
         * @return STMT
         */
        public function prepare($sql = false);

        /**
         * Run a basic query.
         *
         * @param string $sql
         * @return \PHY\Database\IResult
         */
        public function query($sql = false);

        /**
         * Run multiple queries.
         *
         * @param string $sql
         * @return \PHY\Database\IResult
         */
        public function multi_query($sql = false);

        /**
         * Clean a string.
         *
         * @param string $string
         * @return string
         */
        public function clean($string = false);

        /**
         * Set a manager to use with this class.
         *
         * @param \PHY\Database\IManager $manager
         * @return \PHY\Database\MySQLi
         */
        public function setManager(\PHY\Database\IManager $manager);

        /**
         * Grab our manager.
         *
         * @return \PHY\Database\MySQLi\Manager
         */
        public function getManager();
    }
