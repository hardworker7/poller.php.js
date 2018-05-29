<?php

    class modele {

        public static $db = false;

        public static function initDb()
        {
            $db = self::getDbInstance();
            $db->createTable([
                ["name" => "poller", "fields" =>[
                    "id INTEGER AUTO_INCREMENT PRIMARY KEY",
                    "token VARCHAR(100) NOT NULL",
                    "last_request VARCHAR(30) NOT NULL"
                ]],
                ["name" => "datas", "fields" => [
                    "id INTEGER AUTO_INCREMENT PRIMARY KEY",
                    "owner VARCHAR(100) NOT NULL",
                    "event VARCHAR(100)",
                    "content TEXT"
                ]],
                ["name" => "vals", "fields" => [
                    "id INTEGER AUTO_INCREMENT PRIMARY KEY",
                    "owner VARCHAR(100) NOT NULL",
                    "key VARCHAR(100)",
                    "value TEXT"
                ]]
            ]);
            modele::$db = $db;
        }

        public static function getDbInstance()
        {
            if (!self::$db) {
                $db = new Sqlite('database/poller.sqlite3');
                $db->connect();
                self::$db = $db;
                return self::$db;
            }
            else {
                return self::$db;
            }
        }
    }