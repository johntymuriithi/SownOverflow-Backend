<?php

use yii\db\Migration;

/**
 * Class m240812_081730_questions
 */
class m240812_081730_questions extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%questions}}', [
            'q_id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'q_description' => $this->string(50000)->notNull(),
            'q_title' => $this->string(500)->notNull(),
            'q_votes' => $this->integer()->defaultValue(0),
            'q_downvotes' => $this->integer()->defaultValue(0),
            'q_date' => $this->string(500)->notNull(),

        ]);

        $this->addForeignKey(
            'fk-user',
            'questions',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-user', 'questions');
        $this->dropTable('{{%questions}}');
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240812_081730_questions cannot be reverted.\n";

        return false;
    }
    */
}
