<?php
namespace app\controllers;

use app\models\Answers;

use app\models\Questions;
use app\models\User;
use Yii;
use yii\data\Pagination;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
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


        $answer->q_id = $params['q_id'];
        $answer->a_description = $params['a_description'];

        if ($answer->save()) {
            return ['status' => 200, 'message' => 'Answer Posted Successfully'];
        } else {
            return ['status' => false, 'errors' => $answer->errors];
        }
    }

    public function actionAnsweredit() {
        Yii::$app->response->format = Response::FORMAT_JSON;
//        $params = Yii::$app->request->bodyParams;
//        $userId = Yii::$app->user->id;

        $answerId = Yii::$app->request->post('id');

        $description = Yii::$app->request->post('a_description');

        $updateCommand = Yii::$app->db->createCommand()
            ->update('answers', ['a_description' => $description], ['a_id' => $answerId])
            ->execute();

        if ($updateCommand) {
            return ["status" => 200, "message" => "updated well"];
        } else {
            throw new BadRequestHttpException("Failed to edit the Answer");
        }
    }

    public function actionAnswerdelete() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $answerId = Yii::$app->request->post('id');

        $answer = Answers::findOne(['a_id' => $answerId]);
        if (!$answer) {
            throw new NotFoundHttpException("Answer not found");
        }
        if ($answer->delete()) {
            return ["status" => 200, "message" => "Answer Deleted well"];
        } else {
            throw new BadRequestHttpException("Failed to delete  the Answer");
        }
    }

    public function actionShowanswers()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Define the number of items per page
        $pageSize = 5;

        // Create a query to fetch questions
        $query = Answers::find();

        // Create a Pagination object and configure it
        $pagination = new Pagination([
            'defaultPageSize' => $pageSize,
            'totalCount' => $query->count(),
        ]);

        // Apply pagination to the query
        $questions = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        if ($questions) {
            $total = [];
            foreach ($questions as $question) {
                $user = User::findOne(['id' => $question->user_id]);
                $reviewData = [
                    'id' => $question->a_id,
                    'content' => $question->a_description,
                    'dateAnswered' => $question->a_date,
                    'user' => [
                        'username' => $user->username,
                        'level' => $user->level
                    ]
                ];

                $total[] = $reviewData;
            }
            return [
                "status" => 200,
                "message" => "Answers retrieved successfully",
                "questions" => $total,
                "pagination" => [
                    'totalCount' => $pagination->totalCount,
                    'pageCount' => $pagination->pageCount,
                    'currentPage' => $pagination->page,
                    'pageSize' => $pagination->pageSize,
                ],
            ];
        } else {
            throw new NotFoundHttpException("No Answers Found");
        }
    }

}
?>