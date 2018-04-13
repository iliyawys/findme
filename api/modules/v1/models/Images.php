<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "images".
 *
 * @property int $id
 * @property string $image
 *
 * @property Applications $app
 */
class Images extends \yii\db\ActiveRecord
{
    /**
     * @return string Model table name
     */
    public static function tableName()
    {
        return 'images';
    }

    /**
     * @return array validate rules
     */
    public function rules()
    {
        return [
            [['image'], 'required'],
            [['image'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return array attributes labels
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'image' => 'Image',
        ];
    }  
}