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

    namespace PHY\View;

    /**
     * Handles the hierarchy of the DOM and makes sure elements and their
     * children are rendered to the page.
     *
     * @package PHY\View\Layout
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Layout
    {

        protected $controller = null;
        protected $blocks = [];
        protected $variables = [];
        protected $rendered = false;

        /**
         * Stringify our class.
         *
         * @return string
         */
        public function __toString()
        {
            return $this->toString();
        }

        /**
         * Add config blocks to use with our layout.
         *
         * @return \PHY\View\Layout
         * @throws \PHY\View\Layout\Exception
         */
        public function addBlocks()
        {
            $configs = func_get_args();
            $app = $this->getController()->getApp();
            foreach ($configs as $key) {
                $file = false;
                foreach ($app->getPath()->getPaths(
                    'design'.DIRECTORY_SEPARATOR.$app->getNamespace().DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.$key.'.json', 'design'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.$key.'.json'
                ) as $check) {
                    if (is_file($check)) {
                        $file = $check;
                        break;
                    }
                }
                if (!$file) {
                    throw new Exception('Cannot load layout config '.$key);
                }
                $FILE = fopen($file, 'r');
                $content = fread($FILE, filesize($file));
                fclose($FILE);
                $content = preg_replace(['#/\*.+?\*/#is'], '', $content);
                $content = json_decode($content);
                $content = (new \PHY\Variable\Obj($content))->toArray();
                foreach ($content as $key => $value)
                    ;
                $this->buildBlocks($key, $value);
            }
            return $this;
        }

        /**
         * Return a block.
         *
         * @param string $block
         * @return \PHY\View\IView
         */
        public function block($block)
        {
            return array_key_exists($block, $this->blocks)
                ? $this->blocks[$block]
                : null;
        }

        /**
         * Set our controller.
         *
         * @param \PHY\Controller\AController $controller
         * @return \PHY\View\Layout
         */
        public function setController(\PHY\Controller\AController $controller)
        {
            $event = new \PHY\Event\Item('layout/controller/before', [
                'view' => $this,
                'controller' => $controller
                ]);
            \PHY\Event::dispatch($event);
            $this->controller = $controller;
            $event->setName('layout/controller/after');
            \PHY\Event::dispatch($event);
            return $this;
        }

        /**
         * Get our working controller.
         * 
         * @return \PHY\Controller\AController
         */
        public function getController()
        {
            return $this->controller;
        }

        /**
         * Get a stringified version of our layout.
         *
         * @return string
         */
        public function toString()
        {
            return (string)$this->block('layout');
        }

        /**
         * Render our layout.
         *
         * @return string
         */
        public function render()
        {
            $this->rendered = true;
            return $this->toString();
        }

        /**
         * Recursively build our blocks starting with 'layout'.
         *
         * @param string $key
         * @param array $config
         * @return \PHY\View\Layout
         * @throws Exception
         */
        public function buildBlocks($key, $config)
        {
            if (array_key_exists('viewClass', $config)) {
                if (strpos($config['viewClass'], '/') !== false) {
                    $viewClass = implode('\\', array_map('ucfirst', explode('/', $config['viewClass'])));
                } else {
                    $viewClass = ucfirst($config['viewClass']);
                }
                $class = '\PHY\View\\'.$viewClass;
                if (!class_exists($class)) {
                    throw new Exception('Cannot find '.$class.' view class.');
                }
                $this->blocks[$key] = new $class($key, $config);
            } else {
                $this->blocks[$key] = new \PHY\View\Block($key, $config);
            }
            $this->blocks[$key]->setLayout($this);
            if ($children = $this->blocks[$key]->getChildren()) {
                foreach ($children as $child => $values) {
                    $this->buildBlocks($child, $values);
                }
            }
            return $this;
        }

    }
