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

    use PHY\Component\IComponent;
    use PHY\Component\Registry;
    use PHY\Http\Exception as HttpException;
    use PHY\Http\IRequest;
    use PHY\Http\Response;
    use PHY\Model\IUser;
    use PHY\View\Layout;

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
    class App
    {

        private $namespace = 'default';
        private $components = [];
        private $environment = 'development';
        private $path = null;
        private $debugger = null;
        private $rootDirectory = '';
        private $publicDirectory = '';
        private $user = null;

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
            } else {
                if ($this->hasComponent($key)) {
                    return $this->getComponent($key);
                } else {
                    return $this->getRegistry()->get($key);
                }
            }
        }

        /**
         * Set a Registry value. If the value already exists then it will fail
         * and a warning will be printed if $graceful is false.
         *
         * @param string $key
         * @param mixed $value
         * @return mixed
         * @throws Exception
         */
        public function set($key, $value)
        {
            if (!is_string($key)) {
                throw new Exception('A registry key must be a string.');
            } else {
                if ($component = $this->parseComponent($key)) {
                    return $component[0]->set($component[1], $value);
                } else {
                    return $this->getRegistry()->set($key, $value);
                }
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
         * @param IComponent $registry
         * @return $this
         */
        public function setRegistry(IComponent $registry)
        {
            $this->addComponent($registry);
            return $this;
        }

        /**
         * Grab our Registry. If one hasn't been defined, we'll start a new one.
         *
         * @return IComponent
         */
        public function getRegistry()
        {
            if (!array_key_exists('registry', $this->components)) {
                $this->addComponent(new Registry);
            }
            return $this->components['registry'];
        }

        /**
         * Set our Path class to use in our App.
         *
         * @param Path $path
         * @return $this
         */
        public function setPath(Path $path)
        {
            $this->path = $path;
            return $this;
        }

        /**
         * Return our global Path.
         *
         * @return Path
         */
        public function getPath()
        {
            if ($this->path === null) {
                $this->setPath(new Path([
                    'root' => $this->getRootDirectory(),
                    'public' => $this->getPublicDirectory(),
                ]));
            }
            return $this->path;
        }

        /**
         * Set  our global Debugger.
         *
         * @param IDebugger $debugger
         */
        public function setDebugger(IDebugger $debugger)
        {
            $this->debugger = $debugger;
        }

        /**
         * Grab our global Debugger class.
         *
         * @return IDebugger
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
         * @param string $environment
         * @return $this
         */
        public function setEnvironment($environment)
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
            return $this->get('config/' . $this->environment . '/development');
        }

        /**
         * Check for a programmed component. Things like databases, cache, or
         * config files.
         *
         * @param string $component
         * @return IComponent or (bool)false if Component isn't found
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
                $className = '\PHY\Component\\' . $component;
                if (class_exists($className)) {
                    $this->addComponent(new $className);
                    return [$this->getComponent($component), $key];
                }
            }
            return false;
        }

        /**
         * Get a component if it exists.
         *
         * @param string $key
         * @return IComponent|bool
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
         * @param string $key
         * @return bool
         */
        public function hasComponent($key)
        {
            return array_key_exists(strtolower($key), $this->components);
        }

        /**
         * Add a component to our App.
         *
         * @param IComponent $component
         * @return $this
         */
        public function addComponent(IComponent $component)
        {
            $component->setApp($this);
            $this->components[$component->getName()] = $component;
            return $this;
        }

        /**
         * Set our user.
         *
         * @param IUser $user
         * @return $this
         */
        public function setUser(IUser $user)
        {
            $this->user = $user;
            return $this;
        }

        /**
         * Get our logged in user.
         *
         * @return IUser
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
         * @param IRequest $request
         * @return null
         * @throws Exception
         */
        public function render(IRequest $request)
        {
            /* Look for a rewrite rule */
            try {
                set_error_handler(function ($number, $message, $file, $line) {
                    throw new \ErrorException($message, $number, $number, $file, $line);
                });
                $manager = $this->get('database/default.manager');
                /* @var \PHY\Model\Rewrite $rewrite */
                $rewrite = $manager->getModel('rewrite');
                $manager->load($rewrite::loadByRequest($request), $rewrite);
                if ($rewrite->exists()) {
                    if ($rewrite->isRedirect()) {
                        $response = new Response($request->getEnvironmentals(), $this->get('config/status_code'));
                        $response->redirect($rewrite->destination, $rewrite->redirect);
                        $response->renderHeaders();
                        exit;
                    } else {
                        $path = $rewrite->destination;
                    }
                } else {
                    $path = $request->getUrl();
                }
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
                        $request->addParameters(array_combine($parameters[0], $parameters[1]));
                    }
                } else {
                    if (count($pathParameters)) {
                        $controllerClass = current($pathParameters);
                        if (!$controllerClass) {
                            $controllerClass = 'index';
                        }
                        $method = 'index';
                    } else {
                        $controllerClass = 'index';
                        $method = 'index';
                    }
                }

                /* @var \PHY\Controller\IController $controller */
                if (class_exists('\PHY\Controller\\' . $controllerClass)) {
                    $_ = '\PHY\Controller\\' . $controllerClass;
                    $controller = new $_($this);
                } else {
                    throw new HttpException\NotFound('Seems I couldn\'t find your requests controller "' . $controllerClass . '". Blame the programmer, he\she almost definitely did it. Even if you put in the wrong url, just blame them. They\'re used to it!');
                }
                $controller->setRequest($request);

                $layout = new Layout;
                $layout->setController($controller);
                $layout->loadBlocks('default', $controllerClass . '/' . $method);
                $controller->setLayout($layout);

                $controller->action($method);
                $response = $controller->render();
            } catch (\Exception $exception) {
                /* @var \PHY\Controller\Error $controller */
                $controller = new Controller\Error($this);
                if ($exception instanceof Database\Exception) {
                    $controller->setMessage('Sorry, yet there was an issue trying to connect to our database. Please try again in a bit');
                } else if ($exception instanceof HttpException) {
                    $controller->httpException($exception);
                } else if ($exception instanceof Exception) {
                    $controller->setMessage('Sorry, but something happened Phyneapple related. Could have been us or our framework. Looking in to it...');
                } else if ($exception instanceof \ErrorException) {
                    $controller->setMessage('Okay, I got this one, seems there is an issue related to the code itself. Hopefully the developer is logging these exceptions.');
                } else {
                    $controller->setMessage('Seems there was general error. We are checking it out.');
                }
                $controller->setException($exception);
                $controller->action('index');
                $response = $controller->render();
            }

            $response->renderHeaders();
            if (!$response->isRedirect()) {
                $response->renderContent();
            }
        }

        /**
         * Set our root directory.
         *
         * @param string $dir
         * @return $this
         */
        public function setRootDirectory($dir)
        {
            $this->rootDirectory = $dir;
            return $this;
        }

        /**
         * Get our root directory, this is the base of everything.
         *
         * @return string
         */
        public function getRootDirectory()
        {
            return $this->rootDirectory;
        }

        /**
         * Set our root directory.
         *
         * @param string $dir
         * @return $this
         */
        public function setPublicDirectory($dir)
        {
            $this->publicDirectory = $dir;
            return $this;
        }

        /**
         * Get our public folder's base path.
         *
         * @return string
         */
        public function getPublicDirectory()
        {
            return $this->publicDirectory;
        }

    }
