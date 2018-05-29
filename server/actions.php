<?php

    $poller->on('hello', function ($data, $poller) {
        $poller->setValue('pseudo', $data);
        $poller->broadcast('hello', $data);
    });

    $poller->on('message', function ($data, $poller) {
        $poller->broadcast('message', $data);
    });