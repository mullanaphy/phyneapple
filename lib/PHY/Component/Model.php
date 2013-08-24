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

    namespace PHY\Component;

    /**
     * Global Model class.
     *
     * @package PHY\Component\Model
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Model extends \PHY\Component\AComponent
    {

        /**
         * {@inheritDoc}
         */
        public function get($key)
        {
            if ($this->has($key)) {
                $class = array_map('ucfirst', explode('/', $key));
                $class = '\PHY\Model\\'.implode('\\', $class);
                $model = new $class;
                $model->setDatabase($this->getApp()->get('database'));
                return $model;
            }
        }

        /**
         * {@inheritDoc}
         */
        public function has($key)
        {
            $class = array_map('ucfirst', explode('/', $key));
            $class = '\PHY\Model\\'.implode('\\', $class);
            return class_exists($class);
        }

    }
