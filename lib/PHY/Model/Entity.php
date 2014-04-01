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

    use PHY\TResources;
    use PHY\Database\IDatabase;

    /**
     * Generic model handling.
     *
     * @package PHY\Model\Item
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    abstract class Entity implements IEntity
    {

        use TResources;

        protected $data = [];
        protected $initial = [];
        protected $_id;
        protected static $_source = [
            'schema' => [
                'primary' => [
                    'table' => 'item',
                    'columns' => [
                        'updated' => 'date',
                        'created' => 'date',
                        'deleted' => 'boolean'
                    ],
                    'id' => 'id'
                ]
            ]
        ];

        /**
         * Initiate the Item class.
         *
         * @param array $data
         */
        public function __construct(array $data = [])
        {
            $this->init($data);
        }

        /**
         * Handle loadBy{$key}($value) calls.
         *
         * @param string $method
         * @param array $parameters
         * @return $this
         */
        public function __call($method, $parameters)
        {
            $method = strtolower($method);
            if (substr($method, 0, 6) === 'loadby') {
                $key = str_replace('loadby', '', $method);
                $value = $parameters[0];
                $database = (isset($parameters[1]) && $parameters[1] instanceof IDatabase)
                    ? $parameters[1]
                    : null;
                return $this->load([$key => $value], $database);
            } else {
                return null;
            }
        }

        /**
         * Return a defined key for the user or a count of rows for $key.
         *
         * @param string $key
         * @return mixed
         */
        public function __get($key)
        {
            return $this->get($key);
        }

        /**
         * Store new data. Use in conjunction with
         * Item::store() and Item::save()
         *
         * @param string $key
         * @param mixed $value
         * @return $this
         */
        public function __set($key, $value)
        {
            return $this->set($key, $value);
        }

        /**
         * See if this initialize class exists.
         *
         * @return bool
         */
        public function exists()
        {
            return $this->data && $this->id();
        }

        /**
         * Initialize our model.
         *
         * @param array $data
         * @return $this
         */
        public function init(array $data = [])
        {
            $initial = [];
            foreach ($this->getSource()['schema'] as $table) {
                foreach ($table['columns'] as $key => $value) {
                    if (array_key_exists($key, $data)) {
                        $initial[$key] = $data[$key];
                    } else {
                        switch ($value) {
                            case 'boolean':
                                $initial[$key] = false;
                                break;
                            case 'id':
                            case 'int':
                            case 'tinyint':
                                $initial[$key] = 0;
                                break;
                            case 'decimal':
                            case 'float':
                                $initial[$key] = 0.0;
                                break;
                            case 'variable':
                            default:
                                $initial[$key] = '';
                                break;
                        }
                    }
                }
            }
            $this->data = $initial;
            $this->initial = $initial;
            return $this;
        }

        /**
         * Set a key to it's corresponding value if it's allowed
         *
         * @param string $key
         * @param mixed $value
         * @return $this
         * @throws Exception
         */
        public function set($key = '', $value = '')
        {
            if (is_array($key)) {
                foreach ($key as $k => $v) {
                    $this->set($k, $v);
                }
            } else {
                if (!array_key_exists($key, $this->data)) {
                    throw new Exception(get_class($this) . ' does not have a key "' . $key . '" defined. Defined keys: "' . join('", "', array_keys($this->data)) . '"');
                } else {
                    if ($this->data[$key] !== $value) {
                        $this->data[$key] = $value;
                    }
                }
            }
            return $this;
        }

        /**
         * If key is set you'll get the value back. Otherwise NULL.
         *
         * @param string $key
         * @return mixed
         */
        public function get($key = '')
        {
            if (array_key_exists($key, $this->data)) {
                return $this->data[$key];
            } else {
                return null;
            }
        }

        /**
         * See if a resource exists.
         *
         * @param string $key
         * @return bool
         */
        public function has($key)
        {
            return array_key_exists($key, $this->data);
        }

        /**
         *
         * @return Collection
         */
        public function getCollection()
        {
            $collection = get_class($this) . '\\Collection';
            return new $collection;
        }

        /**
         * See if a User doesn't exist or is deleted.
         *
         * @return bool
         */
        public function isDeleted()
        {
            return !$this->exists() || !array_key_exists('deleted', $this->data) || $this->data['deleted'];
        }

        /**
         * See if this instance is a new row in the Database or not.
         *
         * @return bool
         */
        public function isNew()
        {
            return !$this->id();
        }

        /**
         * See if this instance's data has been changed.
         *
         * @return bool
         */
        public function isDifferent()
        {
            return $this->initial !== $this->data;
        }

        /**
         * Get our model's id if it's set.
         *
         * @return string
         */
        public function id()
        {
            if ($this->_id === null) {
                $source = $this->getSource();
                $id = array_key_exists('id', $source['schema']['primary'])
                    ? $source['schema']['primary']['id']
                    : 'id';
                $this->_id = $id;
            }
            return array_key_exists($this->_id, $this->data)
                ? $this->data[$this->_id]
                : false;
        }

        /**
         * Get an array of settings.
         *
         * @return array
         */
        public function toArray()
        {
            return $this->data;
        }

        /**
         * Get an array of all changed values.
         *
         * @return array
         */
        public function getChanged()
        {
            if (!$this->isDifferent()) {
                return [];
            }
            $changed = [];
            foreach ($this->data as $key => $value) {
                if ($value !== $this->initial[$key]) {
                    $changed[$key] = $value;
                }
            }
            return $changed;
        }

        /**
         * Get a JSON string of data.
         *
         * @return string JSON encoded values
         */
        public function toJSON()
        {
            return json_encode($this->toArray());
        }

        /**
         * Get our entity's source (schema).
         *
         * @return array
         */
        public function getSource()
        {
            return static::$_source;
        }

    }

