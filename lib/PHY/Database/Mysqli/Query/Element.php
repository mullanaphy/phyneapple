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

    namespace PHY\Database\MySQLi\Query;

    /**
     * Abstract class for all MySQLi Query elements.
     *
     * @package PHY\Database\MySQLi\Query\Element
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    abstract class Element implements \PHY\Database\Query\IElement
    {

        use \PHY\TResources;

        /**
         * {@inheritDoc}
         */
        public function __toString()
        {
            return $this->toString();
        }

        /**
         * {@inheritDoc}
         */
        public function setManager(\PHY\Database\IManager $manager)
        {
            $this->setResource('manager', $database);
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function getManager()
        {
            if (!$this->hasResource('manager')) {
                throw new Exception('Missing a \PHY\Database\IManager object for our element.');
            }
            return $this->getResource('manager');
        }

        /**
         * {@inheritDoc}
         */
        protected function clean($scalar)
        {
            switch (gettype($scalar)) {
                case 'float':
                case 'double':
                    return (float)$scalar;
                    break;
                case 'int':
                    return (int)$scalar;
                default:
                    return is_numeric($scalar)
                        ? (int)$scalar
                        : "'".$this->getManager()->clean($scalar)."'";
            }
        }

        /**
         * Removed Database from our resources to try and cut down on any
         * possible circular references.
         *
         * @ignore
         */
        public function __destruct()
        {
            $this->unsetResource('database');
        }

    }
