<?php

namespace api\modules\v1\models;

use Yii;
use paragraph1\phpFCM\Recipient\Device;
/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $patronymic_name
 * @property string $birthday
 * @property string $phone_number
 * @property string $city
 * @property string $token
 * @property string $device_token
 * @property string $created_at
 * @property string $device_fb_token
 * @property string $vk_id
 * @property string $fb_id
 * @property string $ok_id
 * @property string $email
 *
 * @property Volunteers[] $volunteers
 */
class Users extends \yii\db\ActiveRecord  implements \yii\web\IdentityInterface
{
    const SCENARIO_USER_EDIT = 'edit';

    /**
     * @return string Model table name
     */
    public static function tableName()
    {
        return 'users';
    }

    public function fields() {
        return [
            'first_name', 
            'last_name',
            'patronymic_name',
            'birthday',
            'phone_number',
            'city'
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_USER_EDIT] = ['first_name', 'last_name','patronymic_name','birthday','phone_number','city'];
        return $scenarios;
    }

    /**
     * @return array validate rules
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name'], 'required'],
            [['created_at','device_token','device_fb_token','vk_id','fb_id','ok_id'], 'safe'],
            [['first_name', 'last_name', 'patronymic_name', 'birthday', 'phone_number', 'city', 'token'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return array attributes labels
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'patronymic_name' => 'Patronymic Name',
            'birthday' => 'Birthday',
            'phone_number' => 'Phone Number',
            'city' => 'City',
            'token' => 'Token',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \api\modules\v1\models\Volunteers
     */
    public function getVolunteers()
    {
        return $this->hasMany(Volunteers::className(), ['uid' => 'id']);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @return integer PrimaryKey
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @return integer token
     */
    public function getAuthKey()
    {
        return $this->token;
    }

    public function generateUniqueRandomString($attribute, $length = 32) {
            
        $randomString = Yii::$app->getSecurity()->generateRandomString($length);
                
        if(!$this->findOne([$attribute => $randomString]))
            return $randomString;
        else
            return $this->generateUniqueRandomString($attribute, $length);
                
    }

    /**
     * Validates authorization key
     *
     * @param string $authKey key to validate
     * @return boolean if key provided is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Get user profile
     *
     * @param integer $id user id
     * @return array user info
     */
    public static function getInfo($id)
    {
        $r = Users::find()->where( [ 'id' => $id ] )->one();
        if ($r === null) 
            return false;
        return [
            'id' => $r->id,
            'display_name' => $r->first_name . ' ' . $r->last_name . ' ' . $r->patronymic_name,
            'name_details' => [
                    $r->first_name,
                    $r->last_name,
                    $r->patronymic_name
            ],
            'birthday'     => $r->birthday,
            'phone_number' => $r->phone_number,
            'city'         => $r->city
        ];
    }

    public static function getUser($id)
    {
        return  Users::find()->where( [ 'id' => $id ] )->one();
    }
}