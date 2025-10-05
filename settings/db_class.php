<?php
include_once 'db_cred.php';

/**
 * @version 2.0 (MAMP-safe, port/socket aware, utf8mb4)
 */
if (!class_exists('db_connection')) {
    class db_connection
    {
        /** @var mysqli|null */
        public $db = null;

        /** @var mysqli_result|false|null */
        public $results = null;

        /** Establish a connection if not already connected */
        private function ensure_connected(): bool
        {
            if ($this->db instanceof mysqli) {
                // already connected and alive?
                if (@mysqli_ping($this->db)) return true;
            }

            // Try TCP (host + port) first
            $host   = defined('SERVER') ? SERVER : '127.0.0.1';
            $user   = defined('USERNAME') ? USERNAME : 'root';
            $pass   = defined('PASSWD') ? PASSWD : '';
            $db     = defined('DATABASE') ? DATABASE : '';
            $port   = defined('PORT') ? PORT : 3306;
            $socket = (defined('SOCKET') && SOCKET) ? SOCKET : null;

            $link = @mysqli_connect($host, $user, $pass, $db, $port);
            if (!$link && $socket) {
                // Fallback to UNIX socket (common on MAMP)
                $link = @mysqli_connect(null, $user, $pass, $db, null, $socket);
            }

            if (!$link) {
                // Log a useful error; you can echo/var_dump during debugging
                error_log('DB CONNECT ERROR: ' . mysqli_connect_error());
                $this->db = null;
                return false;
            }

            // Set charset
            if (!@mysqli_set_charset($link, 'utf8mb4')) {
                error_log('DB CHARSET ERROR: ' . mysqli_error($link));
            }

            $this->db = $link;
            return true;
        }

        /**
         * Backwards-compat: returns boolean like your original
         */
        function db_connect()
        {
            return $this->ensure_connected();
        }

        /**
         * Returns the mysqli connection or false
         */
        function db_conn()
        {
            return $this->ensure_connected() ? $this->db : false;
        }

        /**
         * Run a SELECT (or any read) query; sets $this->results
         */
        function db_query($sqlQuery)
        {
            if (!$this->ensure_connected()) return false;

            $this->results = @mysqli_query($this->db, $sqlQuery);
            if ($this->results === false) {
                // Helpful during debugging; comment out in production
                error_log('DB QUERY ERROR: ' . mysqli_error($this->db) . ' | SQL: ' . $sqlQuery);
                return false;
            }
            return true;
        }

        /**
         * Run INSERT/UPDATE/DELETE
         */
        function db_write_query($sqlQuery)
        {
            if (!$this->ensure_connected()) return false;

            $result = @mysqli_query($this->db, $sqlQuery);
            if ($result === false) {
                error_log('DB WRITE ERROR: ' . mysqli_error($this->db) . ' | SQL: ' . $sqlQuery);
                return false;
            }
            return true;
        }

        /**
         * Fetch one row from last SELECT
         */
        function db_fetch_one($sql)
        {
            if (!$this->db_query($sql)) return false;
            return mysqli_fetch_assoc($this->results);
        }

        /**
         * Fetch all rows from last SELECT
         */
        function db_fetch_all($sql)
        {
            if (!$this->db_query($sql)) return false;
            return mysqli_fetch_all($this->results, MYSQLI_ASSOC);
        }

        /**
         * Count rows from last SELECT
         */
        function db_count()
        {
            if ($this->results === null || $this->results === false) return false;
            return mysqli_num_rows($this->results);
        }

        /**
         * Last auto-increment id
         */
        function last_insert_id()
        {
            return $this->db ? mysqli_insert_id($this->db) : 0;
        }
    }
}
