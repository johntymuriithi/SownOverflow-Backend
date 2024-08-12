<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Answers extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%answers}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'q_id', 'a_description', 'a_date'], 'required'],

        ];
    }
}