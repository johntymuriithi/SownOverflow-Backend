<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Categories extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%categories}}';
    }

    public function rules()
    {
        return [
            [['category_name'], 'required'],
        ];
    }

    public function getQuestions()
    {
        return $this->hasMany(Questions::class, ['category_id' => 'category_id']);
    }
}