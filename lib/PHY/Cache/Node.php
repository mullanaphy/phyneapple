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

    namespace PHY\Cache;

    /**
     * Singular disk/local caching items. Keeps track of their own expirations and
     * values.
     *
     * @package PHY\Cache\Node
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Node
    {

        protected $label = '';
        protected $created = '';
        protected $expires = '';
        protected $content = '';

        /**
         * Initialize a node.
         *
         * @param string $label
         * @param mixed $content
         * @param mixed $expires
         */
        public function __construct($label = '', $content = '', $expires = '')
        {
            $this->created = time();
            $this->setLabel($label);
            $this->setContent($content);
            $this->setExpires($expires);
        }

        /**
         * Set a node's label.
         *
         * @param string $label
         * @return $this
         */
        public function setLabel($label = '')
        {
            $this->label = (string)$label;
            return $this;
        }

        /**
         * Set a node's expiration date.
         *
         * @param mixed $expires
         * @return $this
         */
        public function setExpires($expires = '')
        {
            if (!$expires) {
                return $this;
            } elseif (is_numeric($expires)) {
                $this->expires = $this->created + $expires;
            } else {
                $this->expires = strtotime($expires, $this->created);
            }
            return $this;
        }

        /**
         * Set a node's content.
         *
         * @param mixed $content
         * @return $this
         */
        public function setContent($content = '')
        {
            $this->content = $content;
            return $this;
        }

        /**
         * Get a node's label.
         *
         * @return string
         */
        public function getLabel()
        {
            return $this->label;
        }

        /**
         * Get a node's expiration.
         *
         * @return int
         */
        public function getExpires()
        {
            return $this->expires;
        }

        /**
         * Get a node's content.
         *
         * @return array
         */
        public function getContent()
        {
            return $this->content;
        }

        /**
         * See if a node has expired
         *
         * @return boolean
         */
        public function hasExpired()
        {
            return $this->expires && $this->expires < time();
        }

    }
