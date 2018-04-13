<?php

use yii\db\Migration;

/**
 * Class m171002_090533_images
 */
class m171002_090533_images extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('images', [
            'id' => $this->primaryKey(),
            'image' => $this->string()->notNull(),
        ]);

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('images');
        return false;
    }

}
