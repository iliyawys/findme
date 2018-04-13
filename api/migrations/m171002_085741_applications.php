<?php

use yii\db\Migration;

class m171002_085741_applications extends Migration
{
    public function safeUp()
    {
        $this->createTable('applications', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string()->notNull(),
            'last_name' => $this->string()->notNull(),
            'patronymic_name' => $this->string(),
            'title' => $this->string()->notNull(),
            'description' => $this->string(),
            'last_seen_location' => $this->string()->notNull(),
            'page_views' => $this->integer()->defaultValue(0),
            'comments' => $this->string(),
            'date' => $this->timestamp()
        ]);
        $this->insert('applications', [
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'patronymic_name' => 'patronymic_name',
            'title' => 'title',
            'description' => 'description',
            'last_seen_location' => 'last_seen_location',
            'comments' => 'comments',
        ]);
    }

  
    public function safeDown()
    {
        $this->dropTable('applications');
        return false;
    }

}
