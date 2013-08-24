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
     * Abstract view class. Defines generic methods for various types of views.
     *
     * @package PHY\View\AView
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    abstract class AView implements \PHY\View\IView
    {

        use \PHY\TResources;

        protected $name = '';
        protected $theme = 'default';
        protected $variables = [];

        /**
         * Manipulate our view variables, templates, and such nots in here
         * before __toString() processes a view's data.
         */
        abstract function structure();

        /**
         * Set the name of our view.
         *
         * @param string $name
         * @param array $config
         */
        public function __construct($name = '', array $config = [])
        {
            $this->setName($name);
            $this->setConfig($config);
        }

        /**
         * Render our view.
         *
         * @return string
         */
        public function __toString()
        {
            try {
                return $this->toString();
            } catch (\Exception $e) {
                return '<div class="alert alert-danger"><strong>'.strtoupper(get_class($e)).'</strong> '.$e->getMessage().' ['.$e->getFile().':'.$e->getLine().']</div>';
            }
        }

        /**
         * Set our view's name.
         *
         * @param string $name
         * @return \PHY\View\AView
         */
        public function setName($name = '')
        {
            $this->name = $name;
            return $this;
        }

        /**
         * Get our view's name.
         *
         * @return string
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * Set a Markup builder to use with our view.
         * 
         * @param \PHY\Markup\AMarkup $tag
         * @return \PHY\View\AView
         */
        public function setMarkupBuilder(\PHY\Markup\AMarkup $markup)
        {
            $event = new \PHY\Event\Item('view/markup/before', [
                'object' => $this,
                'markup' => $markup
                ]);
            \PHY\Event::dispatch($event);
            $this->setResource('_markup', $event->markup);
            $event = new \PHY\Event\Item('view/markup/after', [
                'object' => $this,
                'markup' => $event->markup
                ]);
            \PHY\Event::dispatch($event);
            return $this;
        }

        /**
         * Return our Markup Builder.
         * 
         * @return \PHY\Markup\AMarkup
         */
        public function getMarkupBuilder()
        {
            if (!$this->hasResource('_markup')) {
                $this->setMarkupBuilder(new \PHY\Markup\HTML5);
            }
            return $this->getResource('_markup');
        }

        /**
         * Alias for \PHY\Markup\AView::getMarkupBuilder()
         *
         * @return \PHY\Markup\AMarkup
         */
        public function tag()
        {
            return $this->getMarkupBuilder();
        }

        /**
         * Dumps layout class into this object.
         *
         * @return \PHY\View
         */
        public function setLayout(\PHY\View\Layout $layout)
        {
            $this->setResource('layout', $layout);
            $event = new \PHY\Event\Item('view/layout/before', [
                'object' => $this,
                'layout' => $layout
                ]);
            \PHY\Event::dispatch($event);
            $this->setResource('layout', $event->layout);
            $event = new \PHY\Event\Item('view/layout/after', [
                'object' => $this,
                'layout' => $layout
                ]);
            \PHY\Event::dispatch($event);
            return $this;
        }

        /**
         * Get the Layout class.
         *
         * @return \PHY\View\Layout
         */
        public function getLayout()
        {
            if (!$this->hasResource('layout')) {
                throw new Exception('Missing a \PHY\View\Layout layout for this view.');
            }
            return $this->getResource('layout');
        }

        /**
         * Clean up a string.
         *
         * @param string $string
         * @param int $flags
         * @param string $encoding
         * @param boolean $double_encode
         * @return string
         */
        public function clean($string = '', $flags = ENT_QUOTES, $encoding = 'utf-8', $double_encode = false)
        {
            return htmlentities($string, $flags, $encoding, $double_encode);
        }

        /**
         * Get an appropriate url path.
         *
         * @param string $url
         * @param string $location
         * @return string
         */
        public function url($url = '', $location = false)
        {
            $rootPath = $this->getLayout()->getController()->getRequest()->getRootPath();
            if (!$url) {
                return str_replace($_SERVER['DOCUMENT_ROOT'], '', $rootPath.'/');
            }

            if (is_array($url)) {
                $parameters = $url;
                $url = array_shift($parameters);
                $url .= '?'.http_build_query($parameters, '', '&amp;');
            }

            if ($location) {
                $path = $this->getPath();
                foreach ($path->getPaths('resources'.DIRECTORY_SEPARATOR.$this->getTheme().DIRECTORY_SEPARATOR.$location.DIRECTORY_SEPARATOR.$url, 'resources'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.$location.DIRECTORY_SEPARATOR.$url) as $check => $source) {
                    if (is_readable($check)) {
                        return $source;
                    }
                }
            }

            return str_replace($_SERVER['DOCUMENT_ROOT'], '', $rootPath.$url);
        }

        /**
         * Set a theme to use for our view.
         * 
         * @param string $theme
         * @return \PHY\View\AView
         */
        public function setTheme($theme = '')
        {
            $this->theme = $theme;
            return $this;
        }

        /**
         * Get our defined theme.
         *
         * @return string
         */
        public function getTheme()
        {
            return $this->theme;
        }

        /**
         * {@inheritDoc}
         */
        public function setConfig(array $config = [])
        {
            $this->variables = array_replace($this->variables, $config);
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function getConfig()
        {
            return $this->variables;
        }

        /**
         * {@inheritDoc}
         */
        public function setVariable($key = '', $value = '')
        {
            $this->variables[$key] = $value;
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function getVariable($key = '')
        {
            return array_key_exists($key, $this->variables)
                ? $this->variables[$key]
                : null;
        }

        /**
         * {@inheritDoc}
         */
        public function hasVariable($key = '')
        {
            return array_key_exists($key, $this->variables);
        }

        /**
         * {@inheritDoc}
         */
        public function setTemplate($template = '')
        {
            $this->setVariable('template', $template);
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function setPath(\PHY\Path $path)
        {
            $this->setResource('_path', $path);
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function getPath()
        {
            if (!$this->hasResource('_path')) {
                $path = $this->getLayout()->getController()->getApp()->getPath();
                $this->setPath($path);
            }
            return $this->getResource('_path');
        }

        /**
         * Render our view.
         *
         * @return string
         * @throws \Exception
         */
        public function toString()
        {
            $this->structure();
            $source = $this->getVariable('template');
            if (!$source || $this->getVariable('remove')) {
                return '';
            }
            $file = false;
            $paths = $this->getPath()->getPaths(
                'design'.DIRECTORY_SEPARATOR.$this->theme.DIRECTORY_SEPARATOR.'blocks'.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $source), 'design'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'blocks'.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $source)
            );
            foreach ($paths as $check) {
                if (is_file($check) && is_readable($check)) {
                    $file = $check;
                    break;
                }
            }
            if (!$file) {
                throw new \Exception('Source file "'.$file.'" was not found.');
            }

            ob_start();
            extract($this->variables);
            $app = $this->getLayout()->getController()->getApp();
            include $file;
            $content = ob_get_contents();
            ob_end_clean();

            return $content;
        }

        /**
         * JSON encode our toArray version of our view.
         *
         * @return string
         */
        public function toJSON()
        {
            return json_encode($this->toArray());
        }

        /**
         * Return an array version of our view.
         *
         * @return array
         */
        public function toArray()
        {
            return [
                'theme' => $this->theme,
                'source' => $this->source,
                'variables' => $this->variables
            ];
        }

        /**
         * Add\change a given child of our block.
         * 
         * @param string $child
         * @param array $config
         * @return \PHY\View\Block
         */
        public function setChild($child, $config)
        {
            if ($this->hasVariable('children')) {
                $children = $this->getVariable('children');
                $children[$child] = $config;
                $this->setVariable('children', $children);
            } else {
                $this->setVariable('children', [
                    $child => $config
                ]);
            }
            return $this;
        }

        /**
         * Set all the children for a block.
         *
         * @param mixed $children
         * return \PHY\View\Block
         */
        public function setChildren($children)
        {
            $this->setVariable('children', $children);
            return $this;
        }

        /**
         * Get all the block's children.
         * @return array
         */
        public function getChildren()
        {
            return $this->hasVariable('children')
                ? $this->getVariable('children')
                : [];
        }

        /**
         * Get a specific child from our block.
         * 
         * @param string $child
         * @return \PHY\View\IView
         */
        public function child($child)
        {
            $children = $this->getChildren();
            if (array_key_exists($child, $children)) {
                return $this->getLayout()->block($child, $children[$child]);
            }
        }

    }
