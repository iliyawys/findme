<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\Url as url;

class RestController extends ActiveController
{

	/**
     * Default response format
     */
    private $format = 'json';

	public function init()
	{
	    parent::init();
	    \Yii::$app->user->enableSession = false;
	}

	public function behaviors()
	{		
		$behaviors = parent::behaviors();
    	$behaviors['authenticator'] = [
        	'class' => HttpBearerAuth::className(),
    	];
    	return $behaviors;
	}


}