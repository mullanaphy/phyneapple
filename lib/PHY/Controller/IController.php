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
     * Interface for Controllers.
     *
     * @package PHY\Controller\IController
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    interface IController
    {

        /**
         * Run our requested action from /{:controller}/{:action}
         *
         * @param string $method
         */
        public function action($method = 'index');

        /**
         * Grab our current request.
         *
         * @return \PHY\Request
         */
        public function getRequest();

        /**
         * Set our current request.
         *
         * @param \PHY\Request $Request
         * @return \PHY\Controller\IController
         */
        public function setRequest(\PHY\Request $Request);

        /**
         * Grab our current layout.
         *
         * @return \PHY\View\Layout
         */
        public function getLayout();

        /**
         * Set our current layout.
         *
         * @param \PHY\View\Layout $Layout
         * @return \PHY\Controller\IController
         */
        public function setLayout(\PHY\View\Layout $Layout);

        /**
         * GET /{:controller}/index
         */
        public function index_get();

        /**
         * Render a controller.
         *
         * @return \PHY\Response
         */
        public function render();
    }
