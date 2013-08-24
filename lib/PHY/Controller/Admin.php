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
     * Default admin panel. Gives a flavor on how to use Phyneapple based controllers.
     *
     * @package PHY\Controller\Admin
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Admin extends \PHY\Controller\AController
    {

        /**
         * {@inheritDoc}
         */
        public function __construct(\PHY\App $app = null)
        {
            parent::__construct($app);
            $User = $app->getUser();
            if (!$User->exists()) {
                $this->redirect('/login');
            } else {
                $Authorize = $app->get('model/authorize')->loadByRequest('controller/admin');
                if (!$Authorize->exists()) {
                    $Authorize->request = 'controller/admin';
                    $Authorize->allow = 'admin super-admin';
                    $Authorize->deny = 'all';
                    $Authorize->save();
                }
                $Authorize->setUser($User);
                if (!$Authorize->isAllowed()) {
                    $this->redirect('/edgage');
                }
            }
        }

        /**
         * {@inheritDoc}
         */
        public function index_get()
        {

        }

        /**
         * GET /admin/authorize
         */
        public function authorize_get()
        {
            $app = $this->getApp();
            $request = $this->getRequest();
            $id = $request->get('id', false);
            $layout = $this->getLayout();
            if ($id !== false) {
                $Authorize = $app->get('model/authorize')->load($id);
                $config = $layout->config('admin/authorize/item');
                $layout->setConfig($config);
                $content = $layout->addVariables('content', ['Authorize' => $Authorize]);
            } else {
                $config = $layout->config('admin/authorize/collection');
                $layout->setConfig($config);
                $page_id = (int)$request->get('page_id', 1);
                if (!$page_id) {
                    $page_id = 1;
                }
                $limit = (int)$request->get('limit', 50);
                if (!$limit) {
                    $limit = 25;
                }
                $collection = $app->get('model/authorize')->getCollection();
                $collection->setLimit((($page_id * $limit) - $limit), $limit);
                $collection->addFilter('deleted', 0);
                $collection->setSort('request');
                $content = $layout->addVariables('content', ['collection' => $collection]);
                $layout->addChild('content', 'pagination', [
                    'viewClass' => 'pagination',
                    'page_id' => $page_id,
                    'limit' => $limit,
                    'total' => $collection->totalCount(),
                    'url' => [
                        $this->url('admin/authorize'),
                        'limit' => $limit
                    ]
                ]);
            }
            if ($message = $app->get('session/admin/authorize/message')) {
                $app->delete('session/admin/authorize/message');
                $layout->addVariables('message', ['template' => 'admin/authorize/message.phtml', 'message' => $message]);
            }
        }

        /**
         * POST /admin/authorize
         */
        public function authorize_post()
        {
            $request = $this->getRequest();
            $id = (int)$request->get('id', 0);
            if ($id) {
                $Authorize = \PHY\Model\Authorize::load($id);
                if (!$Authorize->exists() || $Authorize->deleted) {
                    $this->getApp()->set('session/admin/authorize/message', 'Sorry, but you cannot edit that privilege.');
                    $this->redirect('/admin/authorize');
                }
            } else {
                $Authorize = new \PHY\Model\Authorize;
            }
            if (!$Authorize->exists()) {
                $Authorize->request = $request->get('request');
            }
            $allow = str_replace([PHP_EOL, "\n", "\r"], ' ', $request->get('allow'));
            $allow = trim(preg_replace('#\s+#', ' ', $allow));
            $deny = str_replace([PHP_EOL, "\n", "\r"], ' ', $request->get('deny'));
            $deny = trim(preg_replace('#\s+#', ' ', $deny));
            $Authorize->allow = $allow;
            $Authorize->deny = $deny;
            $Authorize->save();
            $this->redirect('/admin/authorize/id/'.$Authorize->id);
        }

        /**
         * PUT /admin/authorize
         */
        public function authorize_put()
        {
            $User = new \PHY\Model\User;
            foreach ($this->getRequest()->parameters as $key => $value) {
                $User->set($key, $value);
            }
            $save = $User->save();
            $this->getApp()->set('session/admin/user/message', 'User has been created.');
            $this->redirect('/admin/user/id/'.$save['response']);
        }

        /**
         * GET /admin/user
         */
        public function user_get()
        {
            $request = $this->getRequest();
            $layout = $this->getLayout();
            $id = $request->get('id', false);
            if ($id !== false) {
                $User = \PHY\Model\User::load($id);
                $config = $this->getLayout()->config('admin/user/item');
                $layout->setConfig($config);
                $layout->addVariables('content', ['User' => $User]);
            } else {
                $config = $this->getLayout()->config('admin/user/collection');
                $this->getLayout()->setConfig($config);
                $page_id = (int)$request->get('page_id', 1);
                if (!$page_id) {
                    $page_id = 1;
                }
                $limit = (int)$request->get('limit', 50);
                if (!$limit) {
                    $limit = 25;
                }
                $collection = new \PHY\Model\User\Collection;
                $collection->setLimit((($page_id * $limit) - $limit), $limit);
                $collection->setSort('id');
                $layout->addVariables('content', ['collection' => $collection]);
                $layout->addChild('content', 'pagination', [
                    'viewClass' => 'pagination',
                    'page_id' => $page_id,
                    'limit' => $limit,
                    'total' => $collection->totalCount(),
                    'url' => [
                        $this->url('admin/user/page_id'),
                        'limit' => $limit
                    ]
                ]);
            }
            if ($message = $this->getApp()->get('session/admin/user/message')) {
                $this->getApp()->unset('session/admin/user/message');
                $layout->addVariables('message', [
                    'template' => 'admin/user/message.phtml',
                    'message' => $message
                ]);
            }
        }

        /**
         * POST /admin/user
         */
        public function user_post()
        {
            $request = $this->getRequest();
            $id = (int)$request->get('id', 0);
            if ($id) {
                $User = \PHY\Model\User::load($id);
                if (!$User->exists() || $User->deleted) {
                    $this->getApp()->set('session/admin/user/message', 'Sorry, but you cannot edit that user.');
                    $this->redirect('/admin/user');
                }
            } else {
                $User = new \PHY\Model\User;
            }
            foreach ($request->parameters() as $key => $value) {
                if ($key === 'password' && !$value) {
                    continue;
                }
                $User->set($key, $value);
            }
            $User->save();
            $this->getApp()->set('session/admin/user/message', 'User has been updated.');
            $this->redirect('/admin/user/id/'.$User->id);
        }

        /**
         * PUT /admin/user
         */
        public function user_put()
        {
            $User = new \PHY\Model\User;
            foreach ($this->getRequest()->parameters as $key => $value) {
                $User->set($key, $value);
            }
            $save = $User->save();
            $this->getApp()->set('session/admin/user/message', 'User has been created.');
            $this->redirect('/admin/user/id/'.$save['response']);
        }

    }
