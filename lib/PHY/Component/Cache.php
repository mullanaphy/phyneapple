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
     * Cache namespace
     *
     * @package PHY\Component\Cache
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Cache extends \PHY\Component\AComponent
    {

        /**
         * {@inheritDoc}
         */
        public function get($key)
        {
            $namespace = $this->getNamespace();
            $values = explode('/', $key);
            if ($values) {
                $value = array_shift($values);
            } else {
                $value = $this->getApp()->get('core/component/cache');
            }
            if (!array_key_exists($namespace, $this->resources)) {
                $this->resources[$namespace] = [];
            }
            if (!array_key_exists($value, $this->resources[$namespace])) {
                $cache = false;
                if ($this->getApp()->get('config/cache')) {
                    $config = $this->getApp()->get('config/cache/'.$value);
                    $event = new \PHY\Event\Item('component/cache/load/before', [
                        'config' => $config,
                        'type' => $value
                        ]);
                    \PHY\Event::dispatch($event);
                    $config = $event->config;
                    if ($config) {
                        try {
                            $cache = '\PHY\Cache\\'.$event->type;
                            $cache = new $cache($config);
                            foreach ($config['servers'] as $host) {
                                if (strpos($host, ':') !== false) {
                                    $host = explode(':', $host);
                                    $cache->connect($host[0], $host[1]);
                                } else {
                                    $cache->connect($host);
                                }
                            }
                        } catch (\Exception $e) {
                            $cache = new \PHY\Cache\None;
                        }
                    }
                } else {
                    $config = [];
                    $event = new \PHY\Event\Item('component/cache/load/before', [
                        'config' => $config,
                        'type' => $value
                        ]);
                    \PHY\Event::dispatch($event);
                    $config = $event->config;
                    try {
                        $cache = new \PHY\Cache\Disk;
                    } catch (\Exception $e) {
                        $cache = new \PHY\Cache\None;
                    }
                }
                if ($cache) {
                    $this->resources[$namespace][$value] = $cache;
                    $event = new \PHY\Event\Item('component/cache/load/before', [
                        'object' => $cache,
                        'type' => $value
                        ]);
                } else {
                    throw new \PHY\Exception('Component "cache/'.$value.'" is undefined.');
                }
            }
            return $this->resources[$namespace][$value];
        }

    }
