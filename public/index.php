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

    require '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

    call_user_func(function () {
        /*
         * Make sure we're on PHP5.4+
         */
        if (version_compare(phpversion(), '5.4.0', '<') === true) {
            echo '<html><head><title>Fiddlesticks...</title></head><body><div><h3>Sorry Mate!</h3><p>Phyneapple supports PHP 5.4+.</p></div></body></html>';
            exit;
        }

        /*
         * Initiate a new request from global values.
         * @var \PHY\Http\Request $request
         */
        $request = Http\Request::createFromGlobal();

        /*
         * Setup our app, add a debugger, and start profiling.
         */
        $app = new App;
        $app->setRootDirectory($request->getEnvironmental('PHY_ROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR));
        $app->setPublicDirectory($request->getEnvironmental('PHY_PUBLIC', dirname(__FILE__) . DIRECTORY_SEPARATOR));

        $debugger = new Debugger;
        $debugger->profile(true);
        $app->setDebugger($debugger);

        /*
         * Setup our site and debugging.
         */
        $app->setNamespace($request->getEnvironmental('PHY_NAMESPACE', 'default'));
        $app->setEnvironment($request->getEnvironmental('PHY_ENVIRONMENT', 'development'));

        /*
         * Load any configured events.
         */
        $events = $app->get('config/events');
        if ($events) {
            $app->loadEvents($events);
        }

        /*
         * Add in our logged in user.
         */
        $app->setUser(new Model\User($app->get('session/user')
            ? : array()));

        /*
         * Now let's render our app.
         */
        $app->render($request);
    });
