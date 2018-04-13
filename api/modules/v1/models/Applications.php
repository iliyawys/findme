<?php

namespace api\modules\v1\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "applications".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $patronymic_name
 * @property string $title
 * @property string $description
 * @property string $last_seen_location
 * @property int $page_views
 * @property string $comments
 * @property string $date
 *
 * @property Images[] $images
 * @property Volunteers[] $volunteers
 */
class Applications extends \yii\db\ActiveRecord
{
    public $images;
    public $volunteers;

    /**
     * @return string Model table name
     */
    public static function tableName()
    {
        return 'applications';
    }

    /**
     * @return array Application fields
     */
    public function fields(){
        return [
            'id',
            'first_name',
            'last_name',
            'patronymic_name',
            'title' ,
            'description',
            'last_seen_location',
            'page_views',
            'comments',
            'date',
            'images',
            'volunteers_count'
        ];
    }

    /**
     * @return array validate rules
     */
    public function rules()
    {
        return [
            [['images','first_name', 'patronymic_name', 'last_name', 'title', 'last_seen_location','description','date'], 'required'],
            [['page_views'], 'integer'],
            [['date','images','volunteersCount'], 'safe'],
            [['first_name', 'last_name', 'patronymic_name', 'title', 'description', 'last_seen_location', 'comments'], 'string', 'max' => 255],
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
            'title' => 'Title',
            'description' => 'Description',
            'last_seen_location' => 'Last Seen Location',
            'page_views' => 'Page Views',
            'comments' => 'Comments',
            'date' => 'Date',
        ];
    }

    /**
     * @return \api\modules\v1\models\Images
     */
    public function getImages()
    {
        return $this->hasMany(Images::className(), ['app_id' => 'id']);
    }

    /**
     * @return api\modules\v1\models\Volunteers
     */
    public function getVolunteers()
    {
        return $this->hasMany(Volunteers::className(), ['app_id' => 'id']);
    }

    public function getVolunteers_count()
    {
        return count(ArrayHelper::getColumn(
            $this->getVolunteers()->all(), 'uid'
        ));
    }

    /**
     * Increment page_view counter
     * @param integer $id Application id
     * @return boolean
     */
    public static function viewCounter($id)
    {
        $record = Applications::findOne($id);
        return $record === null ? false : $record->updateCounters(['page_views' => 1]);
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        Volunteers::deleteAll('app_id=' .$this->id);
        return true;
    }

    public function afterSave($insert, $changedAttributes){
        parent::afterSave($insert, $changedAttributes);
        /**
         * send push notification after create application
         */
        
        /*
        $user = Users::getUser(1);
        if ($this->city === null)
            return;
        $users = Users::find()->where(['city' => $this->city])->all();
        foreach ( $users as $user ) {
            if ( $user->device_fb_token === null )
                continue;
            $note = Yii::$app->fcm->createNotification("test title", "testing body");
            $note->setIcon('notification_icon_resource_name')
                ->setColor('#ffffff')
                ->setBadge(1);

            $message = Yii::$app->fcm->createMessage();
            $message->addRecipient(new Device($user->device_fb_token));
            $message->setNotification($note)
                ->setData(['someId' => 111]);

            $response = Yii::$app->fcm->send($message);
        }
        */
    }
}