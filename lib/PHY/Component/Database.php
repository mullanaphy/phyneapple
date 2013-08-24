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
     * Database namespace
     *
     * @package PHY\Component\Database
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Database extends \PHY\Component\AComponent
    {

        /**
         * {@inheritDoc}
         */
        public function get($key, $graceful = false)
        {
            $namespace = $this->getNamespace();
            $values = explode('/', $key);
            if ($values) {
                $value = array_shift($values);
            } else {
                $value = $this->getApp()->get('core/component/database');
            }
            if (!array_key_exists($namespace, $this->resources)) {
                $this->resources[$namespace] = [];
            }
            if (!array_key_exists($value, $this->resources[$namespace])) {
                $database = false;
                $config = $this->getApp()->get('config/database/'.$value);
                $event = new \PHY\Event\Item('component/load/before', [
                    'config' => $config,
                    'type' => $value
                    ]);
                \PHY\Event::dispatch($event);
                if ($event->config && array_key_exists('type', $event->config)) {
                    $database = '\PHY\Database\\'.$event->config['type'];
                    $database = new $database($event->config);
                }
                if ($database) {
                    $this->resources[$namespace][$value] = $database;
                    $event = new \PHY\Event\Item('component/database/load/after', [
                        'object' => $database,
                        'type' => $value
                        ]);
                    \PHY\Event::dispatch($event);
                } else {
                    if (!$graceful) {
                        throw new \PHY\Exception('Component "database/'.$value.'" is undefined.');
                    }
                }
            }
            return $this->resources[$namespace][$value];
        }

    }
