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
     * Abstract class for all MySQLi Query operations.
     *
     * @package PHY\Database\MySQLi\Query\Operation
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    abstract class Operation
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
         * Set a manager to use with our objects.
         * 
         * @param \PHY\Database\IManager $manager
         * @return \PHY\Database\MySQLi\Query\Operation
         */
        public function setManager(\PHY\Database\IManager $manager)
        {
            $this->setResource('manager', $manager);
            return $this;
        }

        /**
         * Return our manager, if none is set, then throw an exception.
         *
         * @return \PHY\Database\IManager
         */
        public function getManager()
        {
            if (!$this->hasResource('manager')) {
                throw new Exception('Missing a \PHY\Database\IManager object for our operation.');
            }
            return $this->getResource('manager');
        }

        /**
         * Set a model to use with our operation.
         *
         * @param \PHY\Model\Entity $model
         * @return \PHY\Database\MySQLi\Query\Operation
         */
        public function setModel(\PHY\Model\Entity $model)
        {
            $this->setResource('model', $model);
            return $this;
        }

        /**
         * Get the defined model for our operation.
         *
         * @return \PHY\Model\Entity
         * @throws \PHY\Database\MySQLi\Operation\Exception
         */
        public function getModel()
        {
            if (!$this->hasResource('model')) {
                throw new \PHY\Database\MySQLi\Operation\Exception('No model has been set for this operation.');
            }
            return $this->getResource('model');
        }

        /**
         * Grab our model's source.
         *
         * @return array
         */
        public function getSource()
        {
            return $this->getModel()->getSource();
        }

    }
