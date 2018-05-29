<?php

    class _Poller {

        private $sessionid;

        public function __construct($pollerid)
        {
            $this->sessionid = $pollerid;
        }

        public function on($event, $handler)
        {
            if (isset($_POST['event']) && $_POST['event'] == $event) {
                $handler($_POST['data'], $this);
            }
        }

        public function emit($event, $datas)
        {
            return PollerMod::addPollerData($this->sessionid, $event, $datas);
        }

        public function broadcast($event, $datas)
        {
            return PollerMod::broadcastPollerData($this->sessionid, $event, $datas);
        }

        public function setValue($key, $value)
        {
            return PollerMod::addPollerValue($this->sessionid, $key, $value);
        }

        public function getValue($key)
        {
            return PollerMod::getPollerValue($this->sessionid, $key);
        }
    }