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

    use PHY\Event;
    use PHY\Event\Item as EventItem;

    /**
     * Head block.
     *
     * @package PHY\View\Head
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Head extends AView
    {

        /**
         * {@inheritDoc}
         */
        public function structure()
        {
            $class = get_class($this->getLayout());
            $class = explode('\\', $class);
            $class = array_slice($class, 2)[0];
            $live = false;
            $controller = $this->getLayout()->getController();
            $app = $controller->getApp();
            $request = $controller->getRequest();
            $path = $app->getPath();
            $cache = $app->get('cache');
            $theme = $app->getNamespace();
            $key = $theme . '/' . $class . '/block/core/head';
            if (!($files = $cache->get($key))) {
                $documentRoot = $request->getEnvironmental('DOCUMENT_ROOT');
                $_files = $this->getVariable('files');
                $files = [
                    'css' => [],
                    'js' => []
                ];
                $merge = [];
                $defaults = [
                    'css' => [
                        'rel' => 'stylesheet',
                        'type' => 'text/css'
                    ],
                    'js' => [
                        'type' => 'text/javascript'
                    ],
                    'key' => [
                        'css' => 'href',
                        'js' => 'src'
                    ]
                ];
                foreach (array_keys($_files) as $type) {
                    foreach ($_files[$type] as $file) {
                        if (is_array($file) || is_object($file)) {
                            $file = (array)$file;
                            $sourceFile = $file[$defaults['key'][$type]];
                            if (strpos($sourceFile, '?') !== false) {
                                $sourceFile = explode('?', $sourceFile)[0];
                            }
                            $source = false;
                            foreach ($path->getPaths('public' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $sourceFile), 'public' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $sourceFile)) as $_source) {
                                if (is_file($_source)) {
                                    $source = $_source;
                                    break;
                                }
                            }
                            if (!$source) {
                                continue;
                            }
                            $file[$defaults['key'][$type]] = str_replace(DIRECTORY_SEPARATOR, '/', str_replace($documentRoot, '', $source));
                            $files[$type][] = array_merge($defaults[$type], $file);
                            continue;
                        } else {
                            if (substr($file, 0, 4) === 'http' || substr($file, 0, 2) === '//') {
                                $files[$type][] = array_merge($defaults[$type], [
                                    $defaults['key'][$type] => $file
                                ]);
                            } else {
                                $sourceFile = $file;
                                if (strpos($sourceFile, '?') !== false) {
                                    $sourceFile = explode('?', $sourceFile)[0];
                                }
                                $source = false;
                                foreach ($path->getPaths('public' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $sourceFile), 'public' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $sourceFile)) as $_source) {
                                    if (is_file($_source)) {
                                        $source = $_source;
                                        break;
                                    }
                                }
                                if (!$source) {
                                    continue;
                                }
                                $merge[$type][$source] = filemtime($source);
                            }
                        }
                    }
                }
                if ($live) {
                    foreach ($merge as $type => $items) {
                        $cached_file = 'resources' . DIRECTORY_SEPARATOR . 'cached' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . md5(implode(array_keys($items)) . implode($items)) . '.' . $type;
                        if (!is_file($cached_file)) {
                            $files_content = '';
                            foreach ($items as $item => $time) {
                                $FILE = fopen($item, 'r');
                                $files_content .= fread($FILE, filesize($item));
                                fclose($FILE);
                            }
                            if (strlen($files_content) > 0) {
                                $FILE = fopen($cached_file, 'w');
                                $minifier = '\PHY\Minify\\' . strtoupper($type);
                                fwrite($FILE, $minifier::minify($files_content));
                                fclose($FILE);
                            }
                        }
                        $files[$type][] = array_merge($defaults[$type], [
                            $defaults['key'][$type] => str_replace(DIRECTORY_SEPARATOR, '/', str_replace($documentRoot, '', $cached_file))
                        ]);
                    }
                    $cache->set($key, $files, time() + 3600);
                } else {
                    foreach ($merge as $type => $items) {
                        foreach ($items as $item => $time) {
                            $files[$type][] = array_merge($defaults[$type], [
                                $defaults['key'][$type] => str_replace(DIRECTORY_SEPARATOR, '/', str_replace($documentRoot, '', $item))
                            ]);
                        }
                    }
                }
            }
            $event = new EventItem('block/core/head', [
                'files' => $files,
                'xsrf_id' => false
            ]);
            Event::dispatch($event);
            $files = $event->files;
            $this->setTemplate('core/sections/head.phtml')->setVariable('css', $files['css'])
                ->setVariable('js', $files['js'])->setVariable('xsrf_id', $event->xsrf_id);
        }

        /**
         * Add files to the header.
         *
         * @param string [, ...] $files
         * @return $this
         */
        public function add()
        {
            $files = func_get_args();
            $_files = $this->getVariable('files');
            foreach ($files as $file) {
                if (is_array($file)) {
                    call_user_func_array([$this, 'add'], $file);
                } else {
                    $extension = explode('.', $file);
                    $_files[$extension[count($extension) - 1]][] = $file;
                }
            }
            $this->setVariable('files', $_files);
            return $this;
        }

    }
