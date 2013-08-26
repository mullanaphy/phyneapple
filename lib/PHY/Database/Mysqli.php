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

    namespace PHY\Database;

    /**
     * Use MySQLi as your database of choice.
     *
     * @package PHY\Database\MySQLi
     * @category PHY\Phyneapple
     * @copyright Copyright (c) 2013 Phyneapple! (http://www.phyneapple.com/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class MySQLi extends \MySQLi implements \PHY\Database\IDatabase
    {

        private $count = 0;
        private $debug = false;
        private $multi = false;
        private $last = false;

        /**
         * Extend this just so we can throw out an error if our Database is
         * acting flaky.
         *
         * @param string $host
         * @param string $username
         * @param string $password
         * @param string $table
         */
        public function __construct(array $settings = [])
        {
            parent::__construct($settings['host'], $settings['username'], $settings['password'], $settings['table']);
            if ($this->connect_error) {
                throw new Exception('Connection Error ('.$this->connect_errno.') '.$this->connect_error);
            }
            return $this;
        }

        /**
         * Turn off Debugging if it was on.
         */
        public function __destruct()
        {
            if ($this->debug) {
                $this->hide();
            }
        }

        /**
         * Prepare a SQL statement.
         *
         * @param string $sql
         * @return MySQLi_STMT
         */
        public function prepare($sql = false)
        {
            ++$this->count;
            $this->multi = false;
            $this->last = $sql;
            if ($this->debug) {
                $this->last();
            }
            $SQL = parent::prepare($sql);
            if ($this->error) {
                throw new Exception($this->error, $sql);
            } else {
                return $SQL;
            }
        }

        /**
         * Run a basic query.
         *
         * @param string $sql
         * @return MySQLi_Result
         */
        public function query($sql = false)
        {
            ++$this->count;
            $this->multi = false;
            $this->last = $sql;
            if ($this->debug) {
                $this->last();
            }
            return parent::query($sql);
        }

        /**
         * Run multiple queries.
         *
         * @param string $sql
         * @return MySQLi_Result
         */
        public function multi_query($sql = false)
        {
            ++$this->count;
            $this->multi = true;
            $this->last = $sql;
            if ($this->debug) {
                $this->last();
            }
            $SQL = parent::multi_query($sql);
            if ($this->error) {
                throw new Exception($this->error, $sql);
            } else {
                return $SQL;
            }
        }

        /**
         * DELETE statement.
         *
         * @param string $sql
         * @return int|bool Returns number of affected rows or false on failure.
         */
        public function delete($sql = false)
        {
            ++$this->count;
            $this->multi = false;
            $this->last = $sql;
            if ($this->debug) {
                $this->last();
            }
            parent::query($sql);
            if ($this->error) {
                throw new Exception($this->error, $sql);
            } else {
                return $this->affected_rows;
            }
        }

        /**
         * INSERT statement.
         *
         * @param string $sql
         * @return insert_id|false Will return false on any error.
         */
        public function insert($sql = false)
        {
            ++$this->count;
            $this->multi = false;
            $this->last = $sql;
            if ($this->debug) {
                $this->last();
            }
            parent::query($sql);
            if ($this->error) {
                throw new Exception($this->error, $sql);
            } else {
                return $this->insert_id;
            }
        }

        /**
         * SELECT statement.
         *
         * @param string $sql
         * @return MySQLi_Result
         */
        public function select($sql)
        {
            ++$this->count;
            $this->multi = false;
            $this->last = $sql;
            if ($this->debug) {
                $this->last();
            }
            $SQL = parent::query($sql);
            if ($this->error) {
                throw new Exception($this->error, $sql);
            } else {
                return $SQL;
            }
        }

        /**
         * UPDATE statement.
         *
         * @param string $sql
         * @return int|bool Returns number of affected rows or false on failure.
         */
        public function update($sql = false)
        {
            ++$this->count;
            $this->multi = false;
            $this->last = $sql;
            if ($this->debug) {
                $this->last();
            }
            parent::query($sql);
            if ($this->error) {
                throw new Exception($this->error, $sql);
            } else {
                return $this->affected_rows;
            }
        }

        /**
         * Alias for real_escape_string.
         *
         * @param string $string
         * @return string
         */
        public function clean($string = false)
        {
            return $this->real_escape_string($string);
        }

        /**
         * Clear out all returned results after using a multi_query.
         */
        public function multi_free()
        {
            if ($this->multi) {
                while ($this->more_results()) {
                    $this->next_result();
                }
            }
            $this->multi = false;
        }

        /**
         * Return a single value from the database.
         *
         * @param string $sql
         * @return mixed
         */
        public function single($sql = false)
        {
            ++$this->count;
            $this->multi = false;
            $this->last = $sql;
            if ($this->debug) {
                $this->last();
            }
            $SQL = parent::query($sql);
            if ($this->error) {
                throw new Exception($this->error, $sql);
            }
            $result = $SQL->fetch_array();
            $SQL->close();
            return isset($result[0])
                ? $result[0]
                : false;
        }

        /**
         * Return a single row from a SELECT statement.
         *
         * @param param $sql
         * @return array
         */
        public function row($sql = false)
        {
            ++$this->count;
            $this->multi = false;
            $this->last = $sql;
            if ($this->debug) {
                $this->last();
            }
            $SQL = parent::query($sql);
            if ($this->error) {
                throw new Exception($this->error, $sql);
            } elseif ($SQL->num_rows > 1) {
                throw new Exception('Your SQL returned '.$SQL->num_rows.' rows. Use select() and fetch_assoc() instead.', $sql);
            }
            $result = $SQL->fetch_assoc();
            $SQL->close();
            return $result;
        }

        /**
         * Turn debugging off.
         */
        public function hide()
        {
            if (!$this->debug) {
                return;
            }
            $debug = debug_backtrace();
            $i = 0;
            echo '<pre style="background:#fee;border:solid 1px #fcc;color:#800;line-height:130%;margin:5px;font:bold 16px \'courier new\';padding:5px;text-align:left;">',
            '<h2 style="border-bottom:solid 2px #fcc;color:#f00;font:inherit;margin:0 0 5px;padding:0;">SQL OUTPUT DEACTIVATED: ', $debug[$i]['file'], '" on line "', $debug[$i]['line'], '".</h2>',
            'SERVERS:      ', implode("\n".'              ', $this->servers), "\n",
            'CALLS:        ', number_format($this->count), "\n",
            'RUNTIME:      ', (round(microtime(true) - $this->debug[0], 5)), ' seconds', "\n",
            'MEMORY USAGE: ', \PHY\Debug::parseBytes(memory_get_usage() - $this->debug[1]),
            '</pre>';
            $this->servers = [array_shift($this->servers)];
            $this->debug = false;
        }

        /**
         * Print out the last run query.
         */
        public function last()
        {
            if (!$this->debug) {
                return;
            }
            $debug = debug_backtrace();
            $i = 1;
            echo '<pre style="background:#eef;border:solid 1px #ccf;line-height:130%;margin:5px;font:12px \'courier new\';padding:5px;text-align:left;color:#008;">',
            '<h2 style="border-bottom:solid 2px #ccf;color:#00f;font:bold 16px \'courier new\';margin:0 0 5px;padding:0;">SQL #', $this->count, ': ', $debug[$i]['file'], '" on line "', $debug[$i]['line'], '" - ', \PHY\Debug::timer(), ', server "', $this->host_info, '"</h2>',
            trim(str_replace(['<', '>'], ['&lt;', '&gt;'], preg_replace('/([\t]+)/is', '', $this->last))), ';',
            '</pre>';
        }

        /**
         * Turn on debugging.
         *
         * @param bool $show WARNING: If set to true it will show on live.
         */
        public function show($show = true)
        {
            if ($show !== true) {
                return;
            }
            $debug = debug_backtrace();
            $i = 0;
            echo '<pre style="background:#fsee;border:solid 1px #fcc;color:#800;line-height:130%;margin:5px;font:bold 16px \'courier new\';padding:5px;text-align:left;">',
            'SQL OUTPUT ACTIVATED: '.$debug[$i]['file'].'" on line "'.$debug[$i]['line'].'"',
            '</pre>';
            $this->debug = [microtime(true), memory_get_usage()];
        }

        /**
         * {@inheritDoc}
         */
        public function setManager(\PHY\Database\IManager $manager)
        {
            $this->manager = $manager;
            $this->manager->setDatabase($this);
            return $this;
        }

        /**
         * {@inheritDoc}
         */
        public function getManager($manager = '')
        {
            if ($this->manager === null) {
                $this->setManager(new \PHY\Database\MySQLi\Manager);
            }
            if ($entity) {
                $model = '\PHY\Model\\'.$entity;
                $this->manager->addModel(new $model);
            }
            return $this->manager;
        }

    }
