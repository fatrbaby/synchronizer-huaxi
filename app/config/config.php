<?php

return [
    "settings" => [
        "displayErrorDetails" => true
    ],
    "pdo.remote" => function () {
		return new PDO("odbc:Driver={SQL Server};Server=127.0.0.1;Database=huaxi", "sa", "123456");
    },
    "pdo.local" => function () {
        return new PDO(sprintf("sqlite:%s/database/equipment.db", realpath(__DIR__ . '/../../')));
    },
    "storage" => function () {
        return new Bridge\Service\Storage\FileStorage(
            new Bridge\Service\Serializer\MsgpackSerializer,
            __DIR__ . '/../../runtime'
        );
    },
    "http" => function () {
        return new GuzzleHttp\Client();
    }
];
