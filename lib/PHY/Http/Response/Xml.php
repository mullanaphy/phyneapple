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

    namespace PHY\Http\Response;

    use PHY\Http\Response;

    /**
     * Handles all the response data.
     *
     * @package PHY\Http\Response\Json
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Xml extends Response
    {

        protected static $_defaultHeaders = [];

        /**
         * {@inheritDoc}
         */
        public function renderContent()
        {
            if ($this->hasContent()) {
                echo $this->xmlParser('', $this->getContent());
            }
        }

        public function setData($data = [])
        {
            $response = new \SimpleXMLElement('<?xml version="1.0"?><response></response>');
            self::array_to_xml($data, $response);
            $this->data = $response->asXML();
            return $this->update();
        }

        /**
         * Make sure we have application/xml if there's no set Content-Type or a
         * Content-Type of text/xml.
         *
         * @return $this
         */
        protected function update()
        {

            if (!$this->headers->has('Content-Type') || 'text/xml' === $this->headers->get('Content-Type')) {
                $this->headers->set('Content-Type', 'application/xml');
            }

            return $this->setContent($this->data);
        }

        /**
         * Recursively add new rows to a SimpleXmlElement.
         *
         * @param array $row
         * @param \SimpleXMLElement &$response
         */
        protected static function array_to_xml($row, &$response)
        {
            foreach ($row as $key => $value) {
                if (is_array($value)) {
                    if (!is_numeric($key)) {
                        $subnode = $response->addChild($key);
                        self::array_to_xml($value, $subnode);
                    } else {
                        $subnode = $response->addChild('item');
                        self::array_to_xml($value, $subnode);
                    }
                } else {
                    if (!is_numeric($key)) {
                        $response->addChild($key, htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false));
                    } else {
                        $response->addChild('item', htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false));
                    }
                }
            }
        }
    }
