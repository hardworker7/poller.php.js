<?php

    class PollActions {
        public static function whenPolling()
        {
            if (isset($_POST['poller'])) {
                if ($polls = self::getPoller($_POST['poller'])) {
                    $polls = self::getPollerData($_POST['poller']);
                    echo json_encode([ "token" => $_POST['poller'], "datas" => $polls ]);
                }
                else {
                    $token = uniqid();
                    if (self::newPoller($token)) {
                        echo json_encode(["token" => $token]);
                    }
                    else {
                        echo json_encode(["error" => true, "message" => "operation failed !"]);
                    }
                }
            }
            else if (isset($_POST['polling'])) {
                $datas = self::getPollerData($_POST['pollerid']);
                self::removePollerData($_POST['pollerid']);
                echo json_encode($datas);
            }
            /*else {
                echo json_encode(["error" => true, "messgae" => "lost !"]);
            }*/
        }

        public static function getPoller($token)
        {
            return PollerMod::getPoller($token);
        }
        
        public static function newPoller($token)
        {
            return PollerMod::createPoller($token);
        }
        
        public static function getPollerData($token)
        {
            return PollerMod::getPollerData($token);
        }
        
        public static function removePollerData($token)
        {
            return PollerMod::removePollerData($token);
        }
        
        public static function setLastRequestDate($token)
        {
            return PollerMod::setLastRequestDate($token);
        }
    }