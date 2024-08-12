<?php

use yii\db\Migration;

/**
 * Class m240812_081803_categories
 */
class m240812_081803_categories extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%categories}}', [
            'category_id' => $this->primaryKey(),
            'category_name' => $this->string(200)->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%categories}}');
    }
}
