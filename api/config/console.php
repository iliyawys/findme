<?php

$params = array_merge(
    require(__DIR__ . '/default-params.php')
);
$db = require(__DIR__ . '/db.php');

return [
    'id'                  => 'api-console',
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log'],
    'controllerNamespace' => 'api\modules\v1\commands',
    'modules'   => [
        'v1' => [
            'basePath' => '@app/modules/v1',
            'class'    => 'api\modules\v1\Module',
        ],
    ],
    'components'          => [
        'db' => $db,
        'fcm' => [
            'class' => 'understeam\fcm\Client',
            'apiKey' => 'AIzaSyAu5J9au0KHzzPiwpmVEqmCNmaz5oCFaCI', 
        ],
        'log' => [
            'targets' => [
                [
                    'class'  => \yii\log\FileTarget::className(),
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'params'              => $params,
];
