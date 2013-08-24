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
     * Default login controller.
     *
     * @package PHY\Controller\Login
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Login extends \PHY\Controller\AController
    {

        /**
         * GET /login
         */
        public function index_get()
        {
            if ($this->getApp()->getUser()->exists()) {
                $this->redirect('');
            }
        }

        /**
         * POST /login
         */
        public function index_post()
        {
            $request = $this->getRequest();
            if (!$request->get('username') || !$request->get('password')) {
                $this->index_get();
                $this->getLayout()->addVariables('error', [
                    'template' => 'user/login/error.phtml',
                    'message' => 'Please enter your Username and\or Password, then try again.',
                    'type' => 'error'
                ]);
            } else {
                $app = $this->getApp();
                $user = $app->get('model/user');
                $user->login($request->get('username'), $request->get('password'));
                if (!$user->isDeleted()) {
                    $this->getApp()->set('session/user', $user->toArray());
                    $redirect = $app->delete('session/_redirect');
                    if (!$redirect) {
                        $redirect = '';
                    }
                    $this->redirect($redirect);
                } else {
                    $this->index_get();
                    $this->getLayout()->addVariables('error', [
                        'template' => 'user/login/error.phtml',
                        'message' => 'Invalid Username and\or Password. Please try again.',
                        'type' => 'error'
                    ]);
                }
            }
        }

    }
