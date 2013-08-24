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
     * Create a pagination block.
     *
     * @package PHY\View\Pagination
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Pagination extends \PHY\View\AView
    {

        /**
         * Generate the block depending on these variables:
         *     'limit' => number of items to show
         *     'total' => total number of items
         *     'page_id' => current page id
         *     'attributes' => attributes to add to the url links
         *     'url' => {
         *         if 'Function' => 'Function'($i) { } will be called for each $i
         *             return a string/array
         *         if 'Array' => 'Array'['page_id'] = $i will be called for each $i
         *         if 'String' => {
         *             if 'QueryString' => appends a page_id=$i to QueryString
         *             if '[%i]' => replaces [%i] with $i
         *             else => appends ?page_id=$i
         */
        public function structure()
        {
            $limit = (int)$this->getVariable('limit', 25);
            $total = (int)$this->getVariable('total', 0);
            $pages = ceil($total / $limit);
            if ($pages > 1) {
                $id = (int)$this->getVariable('page_id', 1);
                if ($id < 1 || $id > $pages) {
                    $id = 1;
                }
                $tag = $this->tag();
                $pagination = $tag->ul;
                $url = $this->getVariable('url', null);
                switch (gettype($url)) {
                    case 'object':
                        if (get_class($url) === 'Closure') {
                            break;
                        } else {
                            $url = (array)$url;
                        }
                    case 'array':
                        $_url = $url;
                        $url = function($i) use($_url) {
                                $_url['page_id'] = $i;
                                return $_url;
                            };
                        break;
                    case 'string':
                        if (strpos($url, '?') !== false) {
                            $_url = explode('?', $url);
                            parse_str($_url[1], $parameters);
                            $url = function($i) use ($_url, $parameters) {
                                    $parameters['page_id'] = $i;
                                    return $_url.'?'.http_build_query($parameters);
                                };
                        } elseif (strpos('[%i]', $url) !== false) {
                            $_url = $url;
                            $url = function($i) use($_url) {
                                    return str_replace('[%i]', $i, $_url);
                                };
                        } else {
                            $_url = $url;
                            $url = function($i) use ($_url) {
                                    return $_url.'?page_id='.$i;
                                };
                        }
                        break;
                    default:
                        $url = function($i) {
                                return $i;
                            };
                }
                $attributes = $this->getVariable('attributes', []);
                $start = 1;
                $end = $pages;
                for ($i = $start; $i <= $end; ++$i) {
                    if ($i === $id) {
                        $page = $tag->strong($i);
                    } else {
                        $page = $tag->helper()->url(
                            $i, $url($i), $attributes
                        );
                    }
                    $pagination->append($tag->li($page));
                }
                $this->setTemplate('pagination.phtml')
                    ->setVariable('pagination', $pagination);
            }
        }

    }
