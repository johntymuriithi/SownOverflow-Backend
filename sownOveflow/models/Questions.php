<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Questions extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%questions}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'q_description', 'q_title', 'q_date', 'category_id'], 'required'],
        ];
    }

    public function getAnswers()
    {
        return $this->hasMany(Answers::class, ['q_id' => 'q_id']);
    }
}