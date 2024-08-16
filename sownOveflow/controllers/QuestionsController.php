<?php
namespace app\controllers;

use app\models\Answers;

use app\models\Categories;
use app\models\Questions;
use app\models\User;
use Yii;
use yii\data\Pagination;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
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


        $question->q_description = $params['q_description'];
        $question->category_id = $params['category_id'];
        $question->q_title = $params['q_title'];

        if ($question->save()) {
            return ['status' => 200, 'message' => 'Question Posted Successfully'];
        } else {
            return ['status' => false, 'errors' => $question->errors];
        }
    }

    public function actionQuestionedit() {
        Yii::$app->response->format = Response::FORMAT_JSON;
//        $params = Yii::$app->request->bodyParams;
//        $userId = Yii::$app->user->id;

        $questionId = Yii::$app->request->post('id');

        $description = Yii::$app->request->post('q_description');

        $updateCommand = Yii::$app->db->createCommand()
            ->update('questions', ['q_description' => $description], ['q_id' => $questionId])
            ->execute();

        if ($updateCommand) {
            return ["status" => 200, "message" => "updated well"];
        } else {
            throw new BadRequestHttpException("Failed to edit the question");
        }
    }

    public function actionShowquestions()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $questions = Questions::find()->with('answers')->all();

        if ($questions) {
            $total = [];
            foreach ($questions as $question) {
                $user = User::findOne(['id' => $question->user_id]);
                $reviewData = [
                    'id' => $question->q_id,
                    'title' => $question->q_title,
                    'content' => $question->q_description,
                    'votes' => $question->q_votes,
                    'dateAsked' => $question->q_date,
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'level' => $user->level
                    ],
                    'answers' => array_map(function($answer) {
                        $user = User::findOne(['id' => $answer->user_id]);
                        return [
                            'id' => $answer->a_id,
                            'content' => $answer->a_description,
                            'votes' => $answer->a_votes,
                            'dateAnswered' => $answer->a_date,
                            'user' => [
                                'id' => $user->id,
                                'username' => $user->username,
                                'level' => $user->level
                            ]
                        ];
                    }, $question->answers),
                ];

                $total[] = $reviewData;
            }
            return [
                "status" => 200,
                "message" => "Questions retrieved successfully",
                "questions" => $total,
                ];
        } else {
            throw new NotFoundHttpException("No Questions Found");
        }
    }

}
?>