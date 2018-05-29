<?php

    class PollerMod extends modele
    {
        public static function initDb()
        {
            modele::initDb();
        }
        public static function createPoller($token)
        {
            return modele::getDbInstance()->insertLine('poller', ["token" => $token, "last_request" => date("d-m-Y H:i:s")]);
        }

        public static function getPoller($token)
        {
            $q = modele::getDbInstance()->pdo()->query("SELECT * FROM poller WHERE token='$token'");
            $r = $q->fetchAll();
            $q->closeCursor();
            return count($r) ? $r[0] : false;
        }

        public static function getPollerData($token)
        {
            $q = modele::getDbInstance()->pdo()->query("SELECT * FROM datas WHERE owner='$token'");
            $r = $q->fetchAll();
            $q->closeCursor();
            return $r;
        }

        public static function removePollerData($token)
        {
            return modele::getDbInstance()->deleteLine('datas', ["owner" => $token]);
        }

        public static function setLastRequestDate($token)
        {
            return modele::getDbInstance()->updateLine('poller', ["last_request" => date("d-m-Y H:i:s")]);
        }

        public static function addPollerData($token, $event, $datas)
        {
            return modele::getDbInstance()->insertLine('datas', ["owner" => $token, "event" => $event, "content" => $datas]);
        }

        public static function broadcastPollerData($token, $event, $datas)
        {
            $pollers = self::getPollers();
            for ($i=0; $i<count($pollers); $i++) {
                if ($pollers[$i]['token']!=$token) {
                    self::addPollerData($pollers[$i]['token'], $event, $datas);
                }
            }
        }

        public static function addPollerValue($token, $key, $value)
        {
            return modele::getDbInstance()->insertLine('vals', ["owner" => $token, "key" => $key, "value" => $value]);
        }

        public static function getPollerValue($token, $key)
        {
            $q = modele::getDbInstance()->pdo()->query("SELECT * FROM vals WHERE owner='$token' AND key='$key' ORDER BY key DESC");
            $r = $q->fetchAll();
            $q->closeCursor();
            return $r[0];
        }

        public static function getPollers()
        {
            $q = modele::getDbInstance()->pdo()->query('SELECT * FROM poller');
            $r = $q->fetchAll();
            $q->closeCursor();
            return $r;
        }
    }