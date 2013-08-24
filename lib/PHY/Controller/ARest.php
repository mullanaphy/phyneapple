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

    namespace PHY\Controller;

    /**
     * Boilerplate abstract class for Controllers.
     *
     * @package PHY\Controller
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    abstract class ARest extends \PHY\Controller\AController implements \PHY\Controller\IRest
    {

        /**
         * Lazy load our Response. If one doesn't exist, we'll create a globally
         * based Request.
         *
         * @return \PHY\Response\Rest
         */
        public function getResponse()
        {
            if ($this->request === null) {
                $event = new \PHY\Event\Item('controller/response/before', [
                    'controller' => $this
                    ]);
                \PHY\Event::dispatch($event);
                $this->request = new \PHY\Response\Rest;
                $event = new \PHY\Event\Item('controller/response/after', [
                    'controller' => $this,
                    'request' => $this->request
                    ]);
                \PHY\Event::dispatch($event);
            }
            return $this->request;
        }

        /**
         * Manually set a response.
         * 
         * @param \PHY\Response\Rest $response
         * @return \PHY\Controller\AController
         */
        public function setResponse(\PHY\Response\Rest $response)
        {
            $this->response = $response;
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function success($message, $status = 200)
        {
            $response = new \PHY\Response\Rest;
            $response->setStatus($status);
            $response->append($message);
            return $response;
        }

        /**
         * {@inheritDoc}
         */
        public function error($message, $status = 500)
        {
            $response = new \PHY\Response\Rest;
            $response->setStatus($status);
            $response->append($message);
            return $response;
        }

    }
