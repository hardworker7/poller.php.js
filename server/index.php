<?php

    header("Access-Control-Allow-Origin: *");

    include_once 'core/Sqlite.php';
    include_once 'core/initdb.php';
    include_once 'core/modele/pollerMod.php';
    include_once 'core/pollactions.php';
    include_once 'core/poller.php';

    PollerMod::initDb();

    PollActions::whenPolling();

    $poller = new _Poller($_POST['pollerid']);

    include_once 'actions.php';