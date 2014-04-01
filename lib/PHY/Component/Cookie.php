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
     * Global Cookie class.
     *
     * @package PHY\Component\Cookie
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Cookie extends AComponent
    {

        /**
         * {@inheritDoc}
         */
        public function delete($key)
        {
            if (headers_sent()) {
                return false;
            }
            if (array_key_exists('PHY_'.$key, $_COOKIE)) {
                unset($_COOKIE['PHY_'.$key]);
                return true;
            }
            return false;
        }

        /**
         * {@inheritDoc}
         */
        public function get($key)
        {
            return array_key_exists('PHY_'.$key, $_COOKIE)
                ? $_COOKIE['PHY_'.$key]
                : null;
        }

        /**
         * {@inheritDoc}
         */
        public function has($key)
        {
            return array_key_exists('PHY_'.$key, $_COOKIE);
        }

        /**
         * {@inheritDoc}
         */
        public function set($key, $value)
        {
            if (headers_sent()) {
                throw new Exception('Cannot declare a cookie after headers have been sent.');
            } else {
                if (!is_string($key)) {
                    throw new Exception('A cookie key must be a string.');
                }
            }
            $_COOKIE['PHY_'.$key] = $value;
            return true;
        }

    }
