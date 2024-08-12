<?php
namespace app\controllers;

use app\models\Answers;

use Yii;
use yii\web\Response;


class AnswersController extends BaseController
{
    public $modelClass = 'app\models\Answers';


    public function actionAnswerpost()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->bodyParams;
        $userId = Yii::$app->user->id;
        $answer = new Answers();
        $answer->q_id = Yii::$app->request->post('q_id');
        $answer->a_description = Yii::$app->request->post('a_description');
        $answer->a_date = Yii::$app->request->post('a_date');
        $answer->user_id = $userId;


        $answer->q_id = $params('q_id');
        $answer->a_description = $params('a_description');
        $answer->a_date = $params('a_date');
        $answer->user_id = $userId;

        if ($answer->save()) {
            return ['status' => 200, 'message' => 'Answer Posted Successfully'];
        } else {
            return ['status' => false, 'errors' => $answer->errors];
        }
    }

}
?>