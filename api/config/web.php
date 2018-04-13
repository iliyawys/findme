<?php

$params = array_merge(
    require(__DIR__ . '/default-params.php')
);
$db = require(__DIR__ . '/db.php');

return [
    'id'        => 'findme',
    'basePath'  => dirname(__DIR__),
    'bootstrap' => ['log', 'api\components\Setup'],
    'modules'   => [
        'v1' => [
            'basePath' => '@app/modules/v1',
            'class'    => 'api\modules\v1\Module',
        ],
    ],
    'aliases' => [
        '@vendor' => '@api/../vendor',
        '@bower' => '@vendor/bower',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'log'                  => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => \yii\log\FileTarget::className(),
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'fcm' => [
            'class' => 'understeam\fcm\Client',
            'apiKey' => 'AIzaSyAu5J9au0KHzzPiwpmVEqmCNmaz5oCFaCI', 
        ],
        'db' => $db,
        'user' => [
            'identityClass' => 'api\modules\v1\models\Users',
            'enableSession' => false
        ],
        'urlManager'           => [
            'enablePrettyUrl'     => true,
            'enableStrictParsing' => true,
            'showScriptName'      => false,
            'rules'               => [
                'doc' => 'site/doc',
                'api' => 'site/api',
                [
                    'class'      => \yii\rest\UrlRule::className(),
                    'pluralize'  => false,
                    'controller' => [
                        'v1/applications'
                    ],
                    'tokens'     => [
                        '{id}'  => '<id:\\d+>',
                        '{type}' => '<type:\\w+>',
                    ],
                    'extraPatterns' => [
                        '{id}/view'        => 'counter',
                        'POST {id}/volunteer'   => 'join',
                        '{id}/volunteer' => 'leave'
                    ],
                ],
                [
                    'class'      => \yii\rest\UrlRule::className(),
                    'pluralize'  => false,
                    'controller' => [
                        'v1/users'
                    ],
                    'only' => ['info','edit','auth_vk','auth_fb','auth_ok'],
                    'tokens'     => [
                        '{id}' => '<id:\\w+>',
                    ],
                    'extraPatterns' => [
                        'PATCH self' => 'edit',
                        '{id}' => 'info'
                    ],
                ],
                'v1/auth/ok' => 'v1/users/auth_ok',
                'v1/auth/fb' => 'v1/users/auth_fb',
                'v1/auth/vk' => 'v1/users/auth_vk',
                'v1/upload/image' => 'v1/applications/upload',
                'v1/device/token/<device_type:\w+>' => 'v1/users/token'

            ],
        ],
        'as contentNegotiator' => [
            'class'   => \yii\filters\ContentNegotiator::className(),
            'formats' => [
                'application/json' => \yii\web\Response::FORMAT_JSON,
            ],
        ],
        'request'              => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'enableCookieValidation' => false,
            'enableCsrfValidation'   => false,
        ],
        'response'             => [
            'format' => \yii\web\Response::FORMAT_JSON,
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->statusCode === 422) {
                    $response->data = [ "errors" => $response->data ];
                }
                if ($response->data !== null && is_array($response->data) && !Yii::$app->request->get('suppress_response_code')) {

                    if (isset($response->data['code']))
                        unset($response->data['code']);
                    if (isset($response->data['type']))
                        unset($response->data['type']);
                    if (isset($response->data['status']))
                        unset($response->data['status']);
                    if (isset($response->data['name']))
                        unset($response->data['name']);
                    
                    if ( (isset($response->data['message']) && 
                        $response->data['message'] === "Page not found." ) 
                        || $response->statusCode === 405 ) {
                            $response->data = null;
                            return;//return $response->statusCode = 405;
                    }
                }
                if ($response->data !== null && Yii::$app->request->get('suppress_response_code')) {
                    $response->data = [
                        'success' => $response->isSuccessful,
                        'data' => $response->data,
                    ];
                    $response->statusCode = 200;
                }
            },
        ],
    ],
    'params' => $params,
    'as beforeAction' => [
        'class' => \yii\filters\Cors::className(),
        'cors'  => [
            'Origin'                         => ['*'],
            'Access-Control-Request-Method'  => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
            'Access-Control-Request-Headers' => ['*'],
        ],
    ],
]; 
