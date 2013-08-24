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

    namespace PHY;

    /**
     * Handles all the response data.
     *
     * @package PHY\Response
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Response
    {

        protected $headers = [];
        protected $content = [];
        protected $layout = null;
        protected $redirect = false;
        protected $redirectStatus = 301;
        protected $statusCode = 200;
        protected static $_defaultHeaders = [];

        /**
         * See if our current response is a redirect.
         *
         * @return boolean
         */
        public function isRedirect()
        {
            return (bool)$this->redirect;
        }

        /**
         * Set a redirect instead of a page render.
         *
         * @param string $redirect
         * @param int $redirectStatus
         * @return \PHY\Response
         */
        public function redirect($redirect = false, $redirectStatus = 301)
        {
            $this->redirect = $redirect;
            $this->redirectStatus = $redirectStatus;
            return $this;
        }

        /**
         * Render our headers and flush what we can.
         */
        public function renderHeaders()
        {
            if ($this->isRedirect()) {
                header('Location: '.$this->redirect, $this->redirectStatus);
            } else if ($this->hasHeaders()) {
                foreach ($this->getHeaders() as $key => $value) {
                    header($key.': '.$value);
                }
            }
            flush();
        }

        /**
         * See if we have headers to render.
         *
         * @return boolean
         */
        public function hasHeaders()
        {
            return (bool)count($this->headers);
        }

        /**
         * Get all the defined headers so far.
         *
         * @return array
         */
        public function getHeaders()
        {
            return $this->headers;
        }

        /**
         * Render our response body.
         */
        public function renderContent()
        {
            if ($this->hasContent()) {
                echo implode('', $this->getContent());
            }
        }

        /**
         * See if our response has a body.
         *
         * @return boolean
         */
        public function hasContent()
        {
            return (bool)count($this->content);
        }

        /**
         * Get our response body.
         *
         * @return array
         */
        public function getContent()
        {
            return $this->content;
        }

        /**
         * Add content to our response body.
         *
         * @param mixed $content
         */
        public function addContent($content)
        {
            $this->content[] = $content;
        }

        /**
         * Set a status code.
         *
         * @param int $code
         */
        public function setStatusCode($code = 200)
        {
            $this->statusCode = $code;
        }

    }
