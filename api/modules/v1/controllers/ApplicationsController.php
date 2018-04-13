<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\web\HttpException;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;
use api\modules\v1\models\Applications;
use api\modules\v1\models\Volunteers;
use api\modules\v1\models\Images;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class ApplicationsController extends RestController
{

	public $modelClass = 'api\modules\v1\models\Applications';


     public function behaviors()
     {
          $behaviors = parent::behaviors();
          $behaviors['verbFilter']['actions']['upload'] = ['POST'];
          $behaviors['verbFilter']['actions']['auth_ok'] = ['POST'];
          $behaviors['verbFilter']['actions']['auth_vk'] = ['POST'];
          $behaviors['verbFilter']['actions']['auth_fb'] = ['POST'];
          $behaviors['verbFilter']['actions']['counter'] = ['POST'];
          $behaviors['verbFilter']['actions']['join'] = ['POST'];
          $behaviors['verbFilter']['actions']['leave'] = ['DELETE'];
          return $behaviors;
    } 

     public function beforeAction($action) {
         return parent::beforeAction($action);
     } 

	/**
     * @SWG\Get(path="/v1/applications",
     *     tags={"Applications"},
     *     summary="Get Applications",
     *     description="Get Applications",
     *     produces={"application/json"},
     *
     *     @SWG\Response(
     *         response = 200,
     *         description = " success"
     *     )
     * )
     *
     */

	/**
     * @SWG\Get(path="/v1/applications/{id}",
     *     tags={"Applications"},
     *     summary="Get Application",
     *     description="Get Application",
     *     produces={"application/json"},
     *
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

	/**
     * @SWG\Delete(path="/v1/applications/{id}",
     *     tags={"Applications"},
     *     summary="Delete Application",
     *     description="Delete Application",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *		  in = "path",
     *        name = "id",
     *        required = true,
     *        type = "integer"
     *     ),
     *
     *     @SWG\Response(
     *         response = 204,
     *         description = " success"
     *     )
     * )
     *
     */

	/**
     * @SWG\Post(path="/v1/applications",
     *     tags={"Applications"},
     *     summary="Create Applications",
     *     description="Create Applications",
     *     produces={"application/json"},
     * 	   @SWG\Parameter(
     *        in = "formData",
     *        name = "images",
     *        type = "string"
     *     ),
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
     *        name = "last_seen_location",
     *        type = "string"
     *     ),
     * 	   @SWG\Parameter(
     *        in = "formData",
     *        name = "description",
     *        type = "string"
     *     ),
     * 	   @SWG\Parameter(
     *        in = "formData",
     *        name = "title",
     *        type = "string"
     *     ),
     * 	   @SWG\Parameter(
     *        in = "formData",
     *        name = "date",
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

	/**
     * @SWG\Post(path="/v1/applications/{id}/view",
     *     tags={"Applications"},
     *     summary="Increment view_page counter",
     *     description="Increment view_page counter",
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
	public function actionCounter($id) 
	{
		if ($res = !Applications::viewCounter($id)){
			throw new HttpException(404 ,'Application not found');
		}
		return null;
	}


	/**
     * @SWG\Post(path="/v1/applications/{id}/volunteer",
     *     tags={"Applications"},
     *     summary="Join to volunteer group",
     *     description="join to volunteer group",
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
	public function actionJoin($id) 
	{	
		if($res = !Volunteers::join(Yii::$app->user->getId(),$id)) {
			throw new HttpException(404 ,'Application not found');
		}
		return null;
	}
	
	/**
     * @SWG\Delete(path="/v1/applications/{id}/volunteer",
     *     tags={"Applications"},
     *     summary="Leave volunteer group",
     *     description="Leave volunteer group",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *		  in = "path",
     *        name = "id",
     *        required = true,
     *        type = "integer"
     *     ),
     *
     *     @SWG\Response(
     *         response = 204,
     *         description = " success"
     *     )
     * )
     *
     */
	public function actionLeave($id) 
	{	
		if ($res = !Volunteers::leave(Yii::$app->user->getId(),$id))
		{
			throw new HttpException(404 ,'Group not found');
		}
		return;
	}

	/**
     * @SWG\Post(path="/v1/upload/image",
     *     tags={"Utilities"},
     *     summary="Upload images",
     *     description="Upload images",
     *     produces={"application/json"},
     * 	  @SWG\Parameter(
     *        in = "formData",
     *        name = "images",
     *        type = "file"
     *    ),
     *
     *     @SWG\Response(
     *         response = 200,
     *         description = " success"
     *     )
     * )
     *
     */
	public function actionUpload()
	{
		$modelIds = [];
    		$uploadPhoto = UploadedFile::getInstancesByName('images');
    		if (!count($uploadPhoto)) {
    		    throw new HttpException(403 ,'Missing params `images`');
    		}
		foreach ($uploadPhoto as $file) {
		    if ($file->size) {
		        $ext = pathinfo($file->name);
		        $r = new Images;
			if (!isset($ext['extension'])) 
			    	$ext['extension'] = 'jpeg';
		        $url = uniqid() . '_' . date_timestamp_get(date_create()) . '.' . $ext['extension'];
		        $path = \Yii::getAlias('@webroot') . '/images/' .  $url;
		        $r->image = $url;
		        if ($file->saveAs( $path )) {
		            $r->save();
		            $modelIds[] = $r->id;
		        }
		        if ($r->getErrors()) {
		            return $r->getErrors();
			        }
		        }
		}
		 
		return new ActiveDataProvider([
		    'query' => Images::find()->where(['id' => $modelIds])
		]);
	}

	/**
     * @SWG\Options(
     *	path = "/v1/applications",
     *	tags = {"Applications"},
     *	operationId = "appOptions",
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