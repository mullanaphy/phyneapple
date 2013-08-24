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
     * @package PHY\Controller\AController
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    abstract class AController implements \PHY\Controller\IController
    {

        protected $app = null;
        protected $config = null;
        protected $request = null;
        protected $redirect = null;
        protected $response = null;
        protected $layout = null;
        protected $parsed = false;
        protected static $_design = [];
        protected static $_theme = 'default';

        /**
         * Inject our app into our controller.
         * 
         * @param \PHY\App $app
         */
        public function __construct(\PHY\App $app = null)
        {
            if ($app !== null) {
                $this->setApp($app);
            }
        }

        /**
         * Render our controller if we haven't already.
         */
        public function __destruct()
        {
            if (!$this->parsed) {
                $this->render();
            }
        }

        /**
         * {@inheritDoc}
         * @throws \PHY\Exception\HTTP\NotFound
         */
        public function index_get()
        {
            throw new \PHY\Exception\HTTP\NotFound('No routes were found for this call... Sorry about that.');
        }

        /**
         * {@inheritDoc}
         */
        public function action($action = 'index')
        {
            $app = $this->getApp();

            $event = new \PHY\Event\Item('controller/action/before', [
                'controller' => $this,
                'action' => $action
                ]);
            \PHY\Event::dispatch($event);
            $action = $event->method;
            $request = $this->getRequest();

            /* See which route we should go with, depending on whether those methods exist or not. */
            $actions = [
                $action.'_'.$request->getMethod(),
                $action.'_get',
                'index_'.$request->getMethod()
            ];
            $action = 'index_get';
            foreach ($actions as $check) {
                if (method_exists($this, $check)) {
                    $action = $check;
                    break;
                }
            }

            /* Check our ACL table to see if this user can view the action/method or not. */
            $check = trim(strtolower(str_replace([__NAMESPACE__, '\\'], ['', '/'], get_class($this))), '/');
            $authorize = $app->get('model/authorize')->loadByRequest($check.'/'.$action);
            $authorize->setUser($app->getUser());
            if (!$authorize->isAllowed()) {
                $this->redirect('unauthorized');
            }
            $authorize->loadByRequest($check);
            $authorize->setUser($app->getUser());
            if (!$authorize->isAllowed()) {
                $this->redirect('unauthorized');
            }

            /* If everything is good, let's call the correct route. */
            $this->$action();
            $event = new \PHY\Event\Item('controller/action/after', [
                'controller' => $this,
                'action' => $action
                ]);
            \PHY\Event::dispatch($event);
        }

        /**
         * Get our global app state.
         * 
         * @return \PHY\App
         */
        public function getApp()
        {
            return $this->app;
        }

        /**
         * Set our global app state.
         *
         * @param \PHY\App $app
         * @return \PHY\Controller\AController
         */
        public function setApp(\PHY\App $app)
        {
            $this->app = $app;
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function getRequest()
        {
            if ($this->request === null) {
                $event = new \PHY\Event\Item('controller/request/before', [
                    'controller' => $this
                    ]);
                \PHY\Event::dispatch($event);
                $this->request = \PHY\Request::createFromGlobal();
                $event = new \PHY\Event\Item('controller/request/after', [
                    'controller' => $this,
                    'request' => $this->request
                    ]);
                \PHY\Event::dispatch($event);
            }
            return $this->request;
        }

        /**
         * {@inheritDoc}
         */
        public function setRequest(\PHY\Request $request)
        {
            $this->request = $request;
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function getResponse()
        {
            if ($this->response === null) {
                $event = new \PHY\Event\Item('controller/response/before', [
                    'controller' => $this
                    ]);
                \PHY\Event::dispatch($event);
                $this->response = new \PHY\Response;
                $event = new \PHY\Event\Item('controller/response/after', [
                    'controller' => $this,
                    'response' => $this->response
                    ]);
                \PHY\Event::dispatch($event);
            }
            return $this->response;
        }

        /**
         * {@inheritDoc}
         */
        public function setResponse(\PHY\Response $response)
        {
            $this->response = $response;
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function getLayout()
        {
            if ($this->layout === null) {
                $event = new \PHY\Event\Item('controller/layout/before', [
                    'controller' => $this
                    ]);
                \PHY\Event::dispatch($event);
                $this->layout = new \PHY\View\Layout;
                $this->layout->setController($this);
                $event = new \PHY\Event\Item('controller/layout/after', [
                    'controller' => $this,
                    'layout' => $this->layout
                    ]);
                \PHY\Event::dispatch($event);
            }
            return $this->layout;
        }

        /**
         * {@inheritDoc}
         */
        public function setLayout(\PHY\View\Layout $Layout)
        {
            $this->layout = $Layout;
            return $this;
        }

        /**
         * Generate a pathed url. Localtion
         *
         * @param string $url
         * @param string $location
         * @return string
         */
        public function url($url = '', $location = false)
        {
            if (!$url) {
                return str_replace($this->getRequest()->getEnvironmental('DOCUMENT_ROOT', ''), '', $this->getApp()->getRootDir().'/');
            }

            if (is_array($url)) {
                $parameters = $url;
                $url = array_shift($parameters);
                $url .= '?'.http_build_query($parameters, '', '&amp;');
            }

            if ($location) {
                $path = $this->getApp()->getPath();
                $routes = $path->getRoutes();
                foreach ($routes as $route) {
                    $paths[$route.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$this->getApp()->getNamespace().DIRECTORY_SEPARATOR.$location.DIRECTORY_SEPARATOR.$url] = DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$this->getApp()->getNamespace().DIRECTORY_SEPARATOR.$location.DIRECTORY_SEPARATOR.$url;
                    $paths[$route.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.$location.DIRECTORY_SEPARATOR.$url] = DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$this->getApp()->getNamespace().DIRECTORY_SEPARATOR.$location.DIRECTORY_SEPARATOR.$url;
                }
                foreach ($paths as $check => $source) {
                    if (is_readable($check)) {
                        return $source;
                    }
                }
            }

            return $url;
        }

        /**
         * Set a redirect instead of rendering the page.
         *
         * @param string|array $redirect
         * @return \PHY\Response
         */
        public function redirect($redirect = '')
        {
            $response = new \PHY\Response;
            if (is_array($redirect)) {
                $parameters = $redirect;
                $redirect = array_shift($parameters);
                $redirect .= '?'.http_build_query($parameters);
            }
            $response->redirect($redirect);
            return $response;
        }

        /**
         * Let's render our Controller and return a response.
         *
         * @return \PHY\Response
         */
        public function render()
        {
            $this->parsed = true;
            $response = $this->getResponse();
            $response->addContent($this->getLayout());
            return $this->getResponse();
        }

    }
