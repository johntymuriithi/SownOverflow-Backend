<?php

use yii\db\Migration;

/**
 * Class m240812_084102_alterquestions
 */
class m240812_084102_alterquestions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn('questions', 'category_id', $this->integer()->notNull());

        $this->addForeignKey(
            'fk-categories',
            'questions',
            'category_id',
            'categories',
            'category_id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-categories', 'questions');
    }
}
