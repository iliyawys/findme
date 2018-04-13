<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\web\Controller;
use yii\httpclient\Client;
use yii\web\HttpException;
use api\modules\v1\models\Users;

class UsersController extends RestController
{

	public $modelClass = 'api\modules\v1\models\Users';

	private $noAuthRoutes = ['v1/users/auth_ok','v1/users/auth_fb','v1/users/auth_vk'];

	public function beforeAction($action)
     {
        /*
         * Remove authentication for specified routes
         */
        if(in_array($action->getUniqueId(), $this->noAuthRoutes))
            $this->detachBehavior('authenticator');

        return parent::beforeAction($action);
     }

     public function behaviors()
     {
          $behaviors = parent::behaviors();
          $behaviors['verbFilter']['actions']['info'] = ['GET'];        
          $behaviors['verbFilter']['actions']['edit'] = ['PATCH'];
          $behaviors['verbFilter']['actions']['token'] = ['PUT'];
          $behaviors['verbFilter']['actions']['auth_ok'] = ['POST'];
          $behaviors['verbFilter']['actions']['auth_vk'] = ['POST'];
          $behaviors['verbFilter']['actions']['auth_fb'] = ['POST'];
          return $behaviors;
     } 
	/**
     * @SWG\Get(path="/v1/users/{id}",
     *     tags={"Users"},
     *     summary="Request User information",
     *     description="Request User information",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *		  in = "path",
     *        name = "id",
     *        required = true,
     *        type = "integer"
     *     ),
     *
     *     @SWG\Response(
     *         response = 200,
     *         description = " success"
     *     )
     * )
     *
     */
	public function actionInfo($id)
	{
		$uid = $id === "self" ? Yii::$app->user->getId(): $id;
		return Users::getInfo($uid);
	}

	/**
     * @SWG\Patch(path="/v1/users/self ",
     *     tags={"Users"},
     *     summary="Edit current User",
     *     description="Edit current User",
     *     produces={"application/json"},
     * 	   @SWG\Parameter(
     *        in = "formData",
     *        name = "first_name",
     *        type = "string"
     *     ),
     * 	   @SWG\Parameter(
     *        in = "formData",
     *        name = "last_name",
     *        type = "string"
     *     ),
     * 	   @SWG\Parameter(
     *        in = "formData",
     *        name = "patronymic_name",
     *        type = "string"
     *     ),
     * 	   @SWG\Parameter(
     *        in = "formData",
     *        name = "birthday",
     *        type = "string"
     *     ),
     * 	   @SWG\Parameter(
     *        in = "formData",
     *        name = "phone_number",
     *        type = "string"
     *     ),
     * 	   @SWG\Parameter(
     *        in = "formData",
     *        name = "city",
     *        type = "string"
     *     ),
     *
     *     @SWG\Response(
     *         response = 200,
     *         description = " success"
     *     )
     * )
     *
     */
	public function actionEdit()
	{	
          $params = Yii::$app->getRequest()->getBodyParams();
		$model = $this->modelClass::findOne(Yii::$app->user->getId());

          if (!count(Yii::$app->getRequest()->getBodyParams()) || 
               (isset($params['first_name']) && empty(trim($params['first_name'])) && 
               isset($params['last_name']) && empty(trim($params['last_name'])))
               ) 
          {
                    // Yii::$app->response->statusCode = 203;
                    return null;
          }

          if (isset($params['first_name']) && !empty(trim($params['first_name'])))
               $model->first_name = $params['first_name'];
          if (isset($params['last_name']) && !empty(trim($params['last_name'])))
               $model->last_name = $params['last_name'];

		if ($model->save() === false && !$model->hasErrors()) {
		            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
		}
 
    		return $model;
	}

	/**
     * @SWG\Put(path="/v1/device/token/{device_type}",
     *     tags={"Users"},
     *     summary="Save device token",
     *     description="Save device token",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *		  in = "path",
     *        name = "device_type",
     *        required = true,
     *        type = "string",
     *		  enum = {"ios","android"}
     *     ),
     *     @SWG\Parameter(
     *		  in = "formData",
     *        name = "device_token",
     *        type = "string"
     *     ),
     *
     *     @SWG\Response(
     *         response = 200,
     *         description = " success"
     *     )
     * )
     *
     */
	public function actionToken($device_type)
	{
		$fields = ['ios','android'];
		if (!in_array($device_type, $fields)) {
			throw new HttpException(405);
		}
		$params = Yii::$app->getRequest()->getBodyParams();
		if (!isset($params['device_token'])){
		    return ['error' => [ "device_token" => "Device token cannot be blank." ]];
		}
		$token = $params['device_token'];
		$model = $this->modelClass::findOne(Yii::$app->user->getId());
		$model->device_token = $device_type . ':' . $token . ';' . $model->device_token; 
		if($model->save())
			return null;
		else {
			throw new HttpException(403 ,'Incorrect params `device_type`');
		}

	}


	/**
     * @SWG\Post(path="/v1/auth/{social_network}",
     *     tags={"Users"},
     *     summary="Autorization social networks",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *		  in = "path",
     *        name = "social_network",
     *        required = true,
     *        type = "string",
     *		  enum = {"ok","vk","fb"}
     *     ),
     *     @SWG\Parameter(
     *		  in = "formData",
     *        name = "social_network_token",
     *        type = "string"
     *     ),
     *
     *     @SWG\Response(
     *         response = 200,
     *         description = " success"
     *     )
     * )
     *
     */
	public function actionAuth_ok()
	{
          //https://api.ok.ru/fb.do?application_key=CBAHHDOLEBABABABA&format=json&method=users.getInfo&sig=7ce79dba798f63ec36416fdfa327f8b0&access_token
          $token = $this->getAuthParams();
          $client = new Client();
          $response = $client->createRequest()
              ->setMethod('get')
              ->setUrl('https://api.ok.ru/fb.do?application_key=CBAHHDOLEBABABABA&format=json&method=users.getInfo&sig=7ce79dba798f63ec36416fdfa327f8b0&access_token='.$token)
              ->send();
          if ($response->isOk) {
               $user = $this->modelClass::find()->where(["ok_id"=>$response->data['id']])->one();
               if ($user === null && isset($response->data['email']))
               $user = $this->modelClass::find()->where(["email"=>$response->data['email']])->one();
                    if ($user === null) {
                         $user = new Users;
                         $user->fb_id = $response->data['id'];
                         $user->first_name = $response->data['first_name'];
                         $user->last_name = $response->data['last_name'];
                         $user->birthday = $response->data['birthday'];
                         if (isset($response->data['email']))
                              $user->email = $response->data['email'];
                    }
               $user->token = $user->generateUniqueRandomString('token');
               if ($user->save()) {
                    return [ "token"=>$user->token ];
               }
          }
		throw new HttpException(403 ,'Incorrect social_network_token');
	}

	public function actionAuth_fb()
	{
		$token = $this->getAuthParams();
		$client = new Client();
		$response = $client->createRequest()
		    ->setMethod('get')
		    ->setUrl('https://graph.facebook.com/me?fields=birthday,name,religion,first_name,last_name,email&access_token='.$token)
		    ->send();
		if ($response->isOk) {
		    $user = $this->modelClass::find()->where(["fb_id"=>$response->data['id']])->one();
		    if ($user === null && isset($response->data['email']))
		    	$user = $this->modelClass::find()->where(["email"=>$response->data['email']])->one();
			if ($user === null) {
				$user = new Users;
				$user->fb_id = $response->data['id'];
				$user->first_name = $response->data['first_name'];
				$user->last_name = $response->data['last_name'];
				$user->birthday = $response->data['birthday'];
				if (isset($response->data['email']))
					$user->email = $response->data['email'];
			}
			$user->token = $user->generateUniqueRandomString('token');
			if ($user->save()) {
				return [ "token"=>$user->token ];
			}
		}
		throw new HttpException(403 ,'Incorrect social_network_token');
	}

	public function actionAuth_vk()
	{
		$token = $this->getAuthParams();
		$client = new Client();
		$response = $client->createRequest()
		    ->setMethod('get')
		    ->setUrl('https://api.vk.com/method/users.get?fields=sex,bdate,city,country,has_mobile,email&access_token='.$token)
		    ->send();
		if ($response->isOk && isset($response->data['response'])) {
			$user = $this->modelClass::find()->where(["vk_id"=>$response->data['response'][0]['uid']])->one();
			if ($user === null)
		    	$user = $this->modelClass::find()->where(["email"=>$response->data['response'][0]['email']])->one();
			if ($user === null) {
				$user = new Users;
				$user->vk_id = $response->data['response'][0]['uid'];
				$user->first_name = $response->data['response'][0]['first_name'];
				$user->last_name = $response->data['response'][0]['last_name'];
				$user->city = $response->data['response'][0]['city'] . '';
				$user->birthday = $response->data['response'][0]['birthday'];
			}
			$user->token = $user->generateUniqueRandomString('token');
			if ($user->save()) {
				return ["token"=>$user->token];
			}
		}
		throw new HttpException(403 ,'Incorrect social_network_token');
		
	}

	/**
     * Get json params for social network auth
     *
     * @param string $social_network_token token
     * @return string $token 
     */
	private function getAuthParams()
	{	
		$params = Yii::$app->getRequest()->getBodyParams();
		if (!isset($params['social_network_token'])){
			throw new HttpException(403 ,'Missing params `social_network_token`');
		}
		return $params['social_network_token'];
	}

	/**
     * @SWG\Options(
     *	path = "/v1/users",
     *	tags = {"Users"},
     *	operationId = "userOptions",
     *	summary = "options",
     *	produces = {"application/json"},
     *	consumes = {"application/json"},
     *	@SWG\Response(
     *     response = 200,
     *     description = "success",
     *     @SWG\Header(header="Allow", type="GET, POST, HEAD, OPTIONS"),
     *     @SWG\Header(header="Content-Type", type="application/json; charset=UTF-8")
     *  )
     *)
     */
}