<?php

namespace app\controllers;

use Yii;
use yii\helpers\Url as url;
use yii\web\Controller;
use light\swagger\SwaggerUIAsset;

class SiteController extends Controller
{
    public function init(){

        // SwaggerUIAsset::register($this);

    }

    public function actions()
    {
        return [
            //The document preview addesss:http://api.yourhost.com/site/doc
            'doc' => [
                'class' => 'light\swagger\SwaggerAction',
                'restUrl' => url::to(['/site/api'], true),
            ],
            'api' => [
                'class' => 'light\swagger\SwaggerApiAction',
                'scanDir' => [
                    Yii::getAlias('@api/modules/v1/swagger'),
                    Yii::getAlias('@api/modules/v1/controllers'),
                    Yii::getAlias('@api/modules/v1/models'),
                ],
                // 'api_key' => 'test'
            ],
        ];
    }


}   