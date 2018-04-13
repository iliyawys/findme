<?php

namespace api\modules\v1\models;

use Yii;
use api\modules\v1\models\Applications;
/**
 * This is the model class for table "volunteers".
 *
 * @property int $id
 * @property int $app_id
 * @property int $uid
 * @property string $date
 *
 * @property Applications $app
 * @property Users $u
 */
class Volunteers extends \yii\db\ActiveRecord
{
    /**
     * @return string Model table name
     */
    public static function tableName()
    {
        return 'volunteers';
    }

    /**
     * @return array validate rules
     */
    public function rules()
    {
        return [
            [['app_id', 'uid'], 'required'],
            [['app_id', 'uid'], 'integer'],
            [['date'], 'safe'],
            [['app_id'], 'exist', 'skipOnError' => true, 'targetClass' => Applications::className(), 'targetAttribute' => ['app_id' => 'id']],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['uid' => 'id']],
        ];
    }

    /**
     * @return array attributes labels
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'app_id' => 'App ID',
            'uid' => 'Uid',
            'date' => 'Date',
        ];
    }

    /**
     * @return \api\modules\v1\models\Applications
     */
    public function getApp()
    {
        return $this->hasOne(Applications::className(), ['id' => 'app_id']);
    }

    /**
     * @return \api\modules\v1\models\Users
     */
    public function getU()
    {
        return $this->hasOne(Users::className(), ['id' => 'uid']);
    }

    /**
     * User join in volunteer group
     * @param $uid User id
     * @param $id Volunteers id
     * @return boolean
     */
    public static function join($uid, $id){
              
        if (Volunteers::find()->where( [ 'uid' => $uid, 'app_id' => $id ] )->exists())
            return ['error'=>'record exist'];
        $record = new Volunteers;
        if (!Applications::find()->where( [ 'id' => $id] )->exists())
            return false;
        $record->app_id = $id;
        $record->uid    = $uid;
        return $record->save();

    }

    /**
     * User leave in volunteer group
     * @param $uid User id
     * @param $id Volunteers id
     * @return boolean
     */
    public static function leave($uid, $id){

        $record = Volunteers::find()->where( [ 'uid' => $uid, 'app_id' => $id ] );
        if (!$record->exists())
            return false;
        return $record->one()->delete();
        
    }

}
