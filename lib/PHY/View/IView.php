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
     * View contract.
     *
     * @package PHY\View\IView
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    interface IView
    {

        /**
         * Method for templates and HTML output.
         */
        public function __toString();

        /**
         * Method for templates and HTML output.
         */
        public function toString();

        /**
         * 
         * @param \PHY\Markup\AMarkup $tag
         * @return \PHY\View\AView
         */
        public function setMarkupBuilder(\PHY\Markup\AMarkup $markup);

        /**
         * Return our Markup Builder.
         * 
         * @return \PHY\Markup\AMarkup
         */
        public function getMarkupBuilder();

        /**
         * Dumps layout class into this object.
         *
         * @return \PHY\View
         */
        public function setLayout(\PHY\View\Layout $layout);

        /**
         * Get the Layout class.
         *
         * @return \PHY\View\Layout
         */
        public function getLayout();

        /**
         * Clean up a string.
         *
         * @param string $string
         * @param int $flags
         * @param string $encoding
         * @param boolean $double_encode
         * @return string
         */
        public function clean($string = '', $flags = ENT_QUOTES, $encoding = 'utf-8', $double_encode = false);

        /**
         * Get an appropriate url path.
         *
         * @param string $url
         * @param string $location
         * @return string
         */
        public function url($url = '', $location = false);

        /**
         * Set a theme to use for our view.
         *
         * @param string $theme
         * @return \PHY\View\AView
         */
        public function setTheme($theme = '');

        /**
         * Get our defined theme.
         *
         * @return string
         */
        public function getTheme();

        /**
         * Set our entire config.
         *
         * @param array $variables
         * @return \PHY\View\IView
         */
        public function setConfig(array $variables = []);

        /**
         * Get our config data.
         *
         * @return array
         */
        public function getConfig();

        /**
         * Set a specific variable.
         *
         * @param string $key
         * @param mixed $value
         * @return \PHY\View\IView
         */
        public function setVariable($key, $value);

        /**
         * Get a specific variable.
         *
         * @param string $key
         * @return mixed
         */
        public function getVariable($key);

        /**
         * See if a specific variable exists.
         *
         * @param string $key
         * @return boolean
         */
        public function hasVariable($key);

        /**
         * Set a specific template file to use for our view.
         *
         * @param string $template
         * @return \PHY\View\IView
         */
        public function setTemplate($template = '');

        /**
         * Set a path to use for config finding and url making.
         *
         * @param \PHY\Path $path
         * @return \PHY\View\IView
         */
        public function setPath(\PHY\Path $path);

        /**
         * Get our path. Grab it from the global registry if one hasn't been
         * injected.
         *
         * @return \PHY\Path
         */
        public function getPath();
    }
