<?php
namespace app\controllers;

use app\models\Answers;

use app\models\Questions;
use Yii;
use yii\web\Response;


class QuestionsController extends BaseController
{
    public $modelClass = 'app\models\Questions';


    public function actionQuestionpost()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->bodyParams;
        $userId = Yii::$app->user->id;
        $question = new Questions();
        $question->category_id = Yii::$app->request->post('category_id');
        $question->q_description = Yii::$app->request->post('q_description');
        $question->q_title = Yii::$app->request->post('q_title');
        $question->q_date = Yii::$app->request->post('q_date');
        $question->user_id = $userId;


        $question->q_id = $params('q_id');
        $question->q_description = $params('q_description');
        $question->category_id = $params('category_id');
        $question->q_title = $params('q_title');
        $question->q_date = $params('a_date');
        $question->user_id = $userId;

        if ($question->save()) {
            return ['status' => 200, 'message' => 'Question Posted Successfully'];
        } else {
            return ['status' => false, 'errors' => $question->errors];
        }
    }

}
?>