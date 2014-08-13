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

    use PHY\Mailer\Mailgun as Mailer;
    use PHY\Model\Config as ConfigModel;
    use PHY\View\Block;
    use PHY\View\Layout;

    /**
     * Contact page.
     *
     * @package PHY\Controller\Contact
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Contact extends AController
    {

        /**
         * GET /contact
         */
        public function index_get()
        {
            $layout = $this->getLayout();
            $head = $layout->block('head');
            $head->setVariable('title', 'Contact');
            $head->setVariable('description', 'Send us some emails or whatever.');

            $sidebar = $layout->block('sidebar');
            $sidebar->setVariable('children', [
                'sidebar/facebook' => [
                    'template' => 'sidebar/facebook.phtml',
                    'viewClass' => 'sidebar/facebook'
                ]
            ]);
        }

        /**
         * POST /contact
         */
        public function index_post()
        {
            $layout = $this->getLayout();
            $fields = $this->getRequest()->get('email', []);
            $error = 'Something seems to have gone astray.';
            try {

                $app = $this->getLayout()->getController()->getApp();
                /**
                 * @var \PHY\Database\IDatabase $database
                 */
                $database = $app->get('database');
                $manager = $database->getManager();
                $email = $manager->load(['key' => 'email'], new ConfigModel);

                $mail = new Mailer($app->get('config/mail'));
                $mail->setTo([$email->value]);
                $mail->setFrom($fields['email']);
                $mail->setSubject('Contact form submission');

                $block = new Block('html', ['fields' => $fields]);
                $block->setLayout($layout);
                $mail->setBody($block->setTemplate('contact/html.phtml')->toString(), 'html');
                $mail->setBody($block->setTemplate('contact/text.phtml')->toString(), 'text');

                $mail->send();

                $mail->setTo([$fields['email']]);
                $mail->setFrom($email->value);
                $mail->setSubject('Thank you for contacting us!');

                $phone = $manager->load(['key' => 'phone'], new ConfigModel);
                $fields['phone'] = $phone->value;
                $block = new Block('html', $fields);
                $block->setLayout($layout);
                $mail->setBody($block->setTemplate('contact/thanks/html.phtml')->toString(), 'html');
                $mail->setBody($block->setTemplate('contact/thanks/text.phtml')->toString(), 'text');

                $mail->send();

                $success = true;
            } catch (\Exception $e) {
                $success = false;
                $error = $e->getMessage();
            }
            if ($success) {
                $this->index_get();
                $content = $layout->block('content');
                $content->setChild('contact/message', [
                    'template' => 'generic/message/success.phtml',
                    'message' => 'Thank you for your submission, I should get back to you within 24 hours.'
                ]);
                foreach ($fields as $field => $value) {
                    $content->setVariable($field, $value);
                }
            } else {
                $content = $layout->block('content');
                $content->setChild('contact/message', [
                    'template' => 'generic/message/error.phtml',
                    'message' => $error,
                    ''
                ]);
            }
        }

    }
