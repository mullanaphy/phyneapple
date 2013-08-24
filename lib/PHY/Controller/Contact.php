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
     * Contact page.
     *
     * @package PHY\Controller\Contact
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Contact extends \PHY\Controller\AController
    {

        /**
         * GET /contact
         */
        public function index_get()
        {
            $this->getLayout()->addVariables('head', [
                'title' => 'Contact'
            ]);
        }

        /**
         * POST /contact
         */
        public function index_post()
        {
            $layout = $this->getLayout();
            $layout->addVariables('head', [
                'title' => 'Contact'
            ]);
            try {
                $fields = $this->getRequest()->get('email', []);
                $mail = new \PHY\Model\Mail;
                $mail->setTemplate('debug.phtml');
                $mail->setContent($fields);
                $mail->setSubject('Contact form submission');
                $mail->send();
                $success = true;
            } catch (\Exception $e) {
                $success = false;
                $layout->addChild('content', 'message', [
                    'template' => 'generic/message/error.phtml',
                    'message' => $e->getMessage()
                ]);
            }
            if ($success) {
                $layout->addChild('content', 'message', [
                    'template' => 'generic/message/success.phtml',
                    'message' => 'Thank you for your submission, I should get back to you within 24 hours.'
                ]);
            }
        }

    }
