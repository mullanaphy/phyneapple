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

    namespace PHY\Model;

    /**
     * For ACL Authorization.
     *
     * @package PHY\Model\Authorize
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Authorize extends \PHY\Model\Entity
    {

        protected static $_source = [
            'schema' => [
                'primary' => [
                    'table' => 'authorize',
                    'columns' => [
                        'request' => 'variable',
                        'allow' => [
                            'type' => 'variable',
                            'comment' => 'There is a space at the beginning and end only in Table view. It is for search reasons.'
                        ],
                        'deny' => [
                            'type' => 'variable',
                            'comment' => 'There is a space at the beginning and end only in Table view. It is for search reasons.'
                        ],
                        'updated' => 'date',
                        'created' => 'date',
                        'deleted' => 'boolean'
                    ]
                ]
            ]
        ];

        /**
         * See if a Request is allowed to be made.
         *
         * @return boolean
         */
        public function isAllowed(\PHY\Model\user $user)
        {
            $allow = explode(' ', $this->data['allow']);
            $deny = explode(' ', $this->data['deny']);

            if ($user->exists()) {
                if (!$user->group) {
                    $user->group = 'all';
                }

                /* If it's root it has full access */
                if ($user->group === 'root') {
                    $allowed = true;
                }

                /* See if a user's ID is in the approved list and not in the denied list. */ elseif (in_array($user->id, $allow) && !in_array($user->id, $deny)) {
                    $allowed = true;
                }

                /* If not, see if he's in the denied list only. */ elseif (in_array($user->id, $deny)) {
                    $allowed = false;
                }

                /* If not, let's see if his group is in the allowed list and it's not in the denied list. */ elseif (in_array($user->group, $allow) && !in_array($user->group, $deny)) {
                    $allowed = true;
                }

                /* If not, let's see if his group is in the denied list. */ elseif (in_array($user->group, $deny)) {
                    $allowed = false;
                }

                /* If not, well let's see if everyone is allowed access and not in the denied list. */ elseif (in_array('all', $allow) && !in_array('all', $deny)) {
                    $allowed = true;
                }

                /* If not, we can see if everyone is denied. */ elseif (in_array('all', $deny)) {
                    $allowed = false;
                }

                /* Finally, if there isn't an explicit DENY all then they should have access. */ else {
                    $allowed = true;
                }
            } else {
                /* There's no logged in user, let's see if everyone is allowed access and not in the denied list. */
                if (in_array('all', $allow) && !in_array('all', $deny)) {
                    $allowed = true;
                }

                /* If not, we can see if everyone is denied. */ elseif (in_array('all', $deny)) {
                    $allowed = false;
                }

                /* Finally, if there isn't an explicit DENY all then they should have access. */ else {
                    $allowed = true;
                }
            }
            return $allowed;
        }

        /**
         * See if a user is denied to do set action.
         *
         * @return bool
         */
        public function isDenied()
        {
            return !$this->isAllowed();
        }

        /**
         * Save changes to the database.
         *
         * @return array Response array.
         */
        public function save()
        {
            if (!$this->isDifferent()) {
                return [
                    'status' => 200,
                    'response' => 'Nothing is different.'
                ];
            }
            $this->set('allow', ' '.$this->get('allow').' ');
            $this->set('deny', ' '.$this->get('deny').' ');
            $save = parent::save();
            $this->set('allow', trim($this->get('allow')));
            $this->set('deny', trim($this->get('deny')));
            return $save;
        }

    }
