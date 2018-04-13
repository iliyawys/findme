<?php

use yii\db\Migration;

/**
 * Class m171002_085843_volunteer
 */
class m171002_085843_volunteers extends Migration
{

    public function safeUp()
    {
        $this->createTable('volunteers', [
            'id' => $this->primaryKey(),
            'app_id' => $this->integer()->notNull(),
            'uid' => $this->integer()->notNull(),
            'date' => $this->dateTime(),
        ]);
        $this->addForeignKey('Volunteer_app_fk_constraint', '{{volunteers}}', 'app_id', '{{applications}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('Volunteer_user_fk_constraint', '{{volunteers}}', 'uid', '{{users}}', 'id', 'RESTRICT', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('volunteers');
        return false;
    }

  
}
