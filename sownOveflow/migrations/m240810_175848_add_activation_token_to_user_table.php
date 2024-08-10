<?php

use yii\db\Migration;

/**
 * Class m240810_175848_add_activation_token_to_user_table
 */
class m240810_175848_add_activation_token_to_user_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'activation_token', $this->string()->unique());
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'activation_token');
    }

}
