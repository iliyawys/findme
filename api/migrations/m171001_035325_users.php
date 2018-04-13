<?php

use yii\db\Migration;

class m171001_035325_users extends Migration
{
    public function safeUp()
    {
        $this->createTable('users', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string()->notNull(),
            'last_name' => $this->string()->notNull(),
            'patronymic_name' => $this->string(),
            'birthday' => $this->string(),
            'phone_number' => $this->string(),
            'city' => $this->string(),
            'token' => $this->string(),
            'device_fb_token' => $this->text(),
            'device_token' => $this->text(),
            'vk_id' => $this->text(),
            'fb_id' => $this->text(),
            'ok_id' => $this->text(),
            'email' => $this->string(),
            'created_at' => $this->timestamp()
        ]);
        $this->insert('users', [
           'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'patronymic_name' => 'Patronymic Name',
            'birthday' => 'Birthday',
            'phone_number' => 'Phone Number',
            'city' => 'City',
            'token' => 'tnPddUFvwQiMTbmDKZY5FPLRIcNygKZk',
        ]);
        $this->insert('users', [
           'first_name' => 'First Name2',
            'last_name' => 'Last Name2',
            'patronymic_name' => 'Patronymic Name2',
            'birthday' => 'Birthday2',
            'phone_number' => 'Phone Number2',
            'city' => 'City',
            'token' => 'tnPddUFvwQiMTbmDKZY5FPLRIcNygKZk',
        ]);
        $this->insert('users', [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'patronymic_name' => 'Patronymic Name',
            'birthday' => 'Birthday',
            'phone_number' => 'Phone Number',
            'city' => 'City2',
            'token' => 'tnPddUFvwQiMTbmDKZY5FPLRIcNygKZk',
        ]);
        $this->insert('users', [
           'first_name' => 'First Name2',
            'last_name' => 'Last Name2',
            'patronymic_name' => 'Patronymic Name2',
            'birthday' => 'Birthday2',
            'phone_number' => 'Phone Number2',
            'city' => 'City2',
            'token' => 'tnPddUFvwQiMTbmDKZY5FPLRIcNygKZk',
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('users');

        return false;
    }

}
