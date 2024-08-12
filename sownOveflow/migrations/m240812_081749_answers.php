<?php

use yii\db\Migration;

/**
 * Class m240812_081749_answers
 */
class m240812_081749_answers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%answers}}', [
            'a_id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'q_id' => $this->integer()->notNull(),
            'a_description' => $this->string(50000)->notNull(),
            'a_votes' => $this->integer()->defaultValue(0),
            'a_date' => $this->string(500)->notNull(),
        ]);

        $this->addForeignKey(
            'fk-user',
            'answers',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-questions',
            'answers',
            'q_id',
            'questions',
            'q_id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-user', 'answers');
        $this->dropForeignKey('fk-questions', 'answers');
        $this->dropTable('{{%answers}}');
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240812_081749_answers cannot be reverted.\n";

        return false;
    }
    */
}
