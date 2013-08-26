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
     * Core APP class. This holds all global states and pieces everything
     * together.
     *
     * @package PHY\APP
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     * @todo Make this make sense. Try and break up what should be global and what should be in the registry class.
     */
    final class App
    {

        private $namespace = 'default';
        private $components = [];
        private $environment = 'development';
        private $path = null;
        private $debugger = null;

        /**
         * Return a value from the Registry if it exists.
         *
         * @param string $key
         * @return mixed|null
         */
        public function get($key)
        {
            if ($component = $this->parseComponent($key)) {
                return $component[0]->get($component[1]);
            } else if ($this->hasComponent($key)) {
                return $this->getComponent($key);
            } else {
                return $this->getRegistry()->get($key);
            }
        }

        /**
         * Set a Registry value. If the value already exists then it will fail
         * and a warning will be printed if $graceful is false.
         *
         * @param string $key
         * @param mixed $value
         * @param bool $graceful
         * @return mixed
         * @throws \PHY\Exception
         */
        public function set($key = null, $value = null)
        {
            if (!is_string($key)) {
                throw new Exception('A registry key must be a string.');
            } else if ($component = $this->parseComponent($key)) {
                return $component[0]->set($component[1], $value);
            } else {
                return $this->getRegistry()->set($key, $value);
            }
        }

        /**
         * Delete this registry key if it exists.
         *
         * @param string $key
         * @param bool $graceful
         * @return bool
         */
        public function delete($key = null, $graceful = false)
        {
            if ($component = $this->parseComponent($key)) {
                return $component[0]->delete($component[1]);
            } else {
                return $this->getRegistry()->delete($key);
            }
        }

        /**
         * Check to see if a key exists. Useful if the ::get you might be using can be
         * false\null and you want to make sure that it was set false and not just a null.
         *
         * @param string $key
         * @return bool
         */
        public function has($key = null)
        {
            if ($component = $this->parseComponent($key)) {
                return $component[0]->has($component[1]);
            } else {
                return $this->getRegistry()->has($key);
            }
        }

        /**
         * Set our registry class to use for our App.
         * 
         * @param \PHY\Component\IComponent $registry
         * @return \PHY\App
         */
        public function setRegistry(\PHY\Component\IComponent $registry)
        {
            $this->addComponent($registry);
            return $this;
        }

        /**
         * Grab our Registry. If one hasn't been defined, we'll start a new one.
         *
         * @return \PHY\Component\Registry
         */
        public function getRegistry()
        {
            if (!array_key_exists('registry', $this->components)) {
                $this->addComponent(new \PHY\Component\Registry);
            }
            return $this->components['registry'];
        }

        /**
         * Set our Path class to use in our App.
         *
         * @param \PHY\Path $path
         * @return \PHY\APP
         */
        public function setPath(Path $path)
        {
            $this->path = $path;
            return $this;
        }

        /**
         * Return our global Path.
         *
         * @return \PHY\Path
         */
        public function getPath()
        {
            if ($this->path === null) {
                $this->setPath(new Path);
            }
            return $this->path;
        }

        /**
         * Set  our global Debugger.
         *
         * @param \PHY\Debugger $debugger
         */
        public function setDebugger(Debugger $debugger)
        {
            $this->debugger = $debugger;
        }

        /**
         * Grab our global Debugger class.
         *
         * @return \PHY\Debugger
         */
        public function getDebugger()
        {
            if ($this->debugger === null) {
                $this->setDebugger(new Debugger);
            }
            return $this->debugger;
        }

        /**
         * Change which namespace to use.
         *
         * @param string $namespace
         * @return string
         */
        public function setNamespace($namespace = null)
        {
            if ($namespace !== null) {
                $this->namespace = $namespace;
            }
            return $this->namespace;
        }

        /**
         * Get the currently defined namespace to use.
         *
         * @return string
         */
        public function getNamespace()
        {
            return $this->namespace;
        }

        /**
         * Set whether we're a development app or in production.
         *
         * @param string $development
         * @return \PHY\App
         */
        public function setEnvironment($environment = false)
        {
            $this->environment = $environment;
            return $this;
        }

        /**
         * See whether we're a development app or in production.
         *
         * @return boolean
         */
        public function isDevelopment()
        {
            return $this->getApp('config/'.$this->environment.'/development');
        }

        /**
         * Check for a programmed component. Things like databases, cache, or
         * config files.
         *
         * @param string $component
         * @return \PHY\Registry\Component or (bool)false if Component isn't found
         */
        private function parseComponent($component)
        {
            if (strpos($component, '/') !== false) {
                $key = explode('/', $component);
                $component = array_shift($key);
                $key = implode('/', $key);
            } else {
                $key = 'default';
            }
            if ($this->hasComponent($component)) {
                return [$this->getComponent($component), $key];
            } else {
                $className = '\PHY\Component\\'.$component;
                if (class_exists($className)) {
                    $this->addComponent(new $className);
                    return [$this->getComponent($component), $key];
                }
            }
            return false;
        }

        /**
         * Get a compontent if it exists.
         * 
         * @param string $component
         * @return \PHY\Component\IComponent|false
         */
        public function getComponent($key)
        {
            $key = strtolower($key);
            return array_key_exists($key, $this->components)
                ? $this->components[$key]
                : false;
        }

        /**
         * See if a component exists.
         *
         * @param string $component
         * @return boolean
         */
        public function hasComponent($key)
        {
            return array_key_exists(strtolower($key), $this->components);
        }

        /**
         * Add a component to our App.
         *
         * @param string $key
         * @param \PHY\Component\IComponent $component
         * @return \PHY\App
         */
        public function addComponent(\PHY\Component\IComponent $component)
        {
            $component->setApp($this);
            $this->components[$component->getName()] = $component;
            return $this;
        }

        /**
         * Set our user.
         * 
         * @param \PHY\Model\User $user
         */
        public function setUser(\PHY\Model\User $user)
        {
            $this->user = $user;
            return $this;
        }

        /**
         * Get our logged in user.
         * 
         * @return \PHY\Model\User
         */
        public function getUser()
        {
            if ($this->user === null) {
                $this->setUser($this->get('session/user'));
            }
            return $this->user;
        }

        /**
         * Render our app.
         *
         * @param \PHY\Request $request
         * @return 
         */
        public function render(\PHY\Request $request)
        {

            $file = dirname(str_replace($_SERVER['DOCUMENT_ROOT'], '', __FILE__));
            if ($file === '/') {
                $file = '';
            }

            /* Look for a rewrite rule */
            try {
                $manager = $this->get('database/default.manager');
                $rewrite = $manager->getModel('rewrite');
                $manager->load($rewrite::loadByRequest($request), $rewrite);
                if ($rewrite->exists()) {
                    if ($rewrite->isRedirect()) {
                        $response = new \PHY\Response;
                        $response->redirect($rewrite->destination, $rewrite->redirect);
                        $response->renderHeaders();
                        exit;
                    } else {
                        $path = $rewrite->destination;
                    }
                }

                $path = $request->getUri();

                $pathParameters = explode('/', strtolower(trim($path, '/')));
                if (count($pathParameters) >= 2) {
                    $controllerClass = array_shift($pathParameters);
                    $method = array_shift($pathParameters);
                    if (count($pathParameters)) {
                        $parameters = [
                            [],
                            []
                        ];
                        $i = 1;
                        foreach ($pathParameters as $key) {
                            $parameters[$i === 0
                                    ? $i = 1
                                    : $i = 0][] = $key;
                        }
                        if (count($parameters[1]) !== count($parameters[0])) {
                            $parameters[1][] = null;
                        }
                        $request->add(array_combine($parameters[0], $parameters[1]));
                    }
                } else if (count($pathParameters)) {
                    $controllerClass = current($pathParameters);
                    if (!$controllerClass) {
                        $controllerClass = 'index';
                    }
                    $method = 'index';
                } else {
                    $controllerClass = 'index';
                    $method = 'index';
                }

                if (class_exists('\PHY\Controller\\'.$controllerClass)) {
                    $_ = '\PHY\Controller\\'.$controllerClass;
                    $controller = new $_($this);
                } else {
                    $controller = new Controller\Index($this);
                }
                $controller->setRequest($request);

                $layout = new \PHY\View\Layout;
                $layout->setController($controller);
                $layout->loadBlocks('default', $controllerClass.'/'.$method);
                $controller->setLayout($layout);

                $controller->action($method);
                $response = $controller->render();
            } catch (\Exception $exception) {
                $controller = new Controller\Error($this);
                try {
                    throw $exception;
                } catch (\PHY\Database\Exception $exception) {
                    $controller->setMessage('Sorry, yet there was an issue trying to connect to our database. Please try again in a bit');
                } catch (\PHY\Exception $exception) {
                    $controller->setMessage('Sorry, but something happened Phyneapple related. Could have been us or our framework. Looking in to it...');
                } catch (\PHY\Exception\HTTP $exception) {
                    $controller->httpException($exception);
                } catch (\Exception $exception) {
                    $controller->setMessage('Seems there was general error. We are checking it out.');
                }
                $controller->action('index');
                $response = $controller->render();
            }

            $response->renderHeaders();
            if (!$response->isRedirect()) {
                $response->renderContent();
            }
        }

    }

