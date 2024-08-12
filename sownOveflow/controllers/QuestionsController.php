<?php
namespace app\controllers;

use app\models\Answers;

use app\models\Categories;
use app\models\Questions;
use app\models\User;
use Yii;
use yii\data\Pagination;
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

    public function actionShowquestions()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Define the number of items per page
        $pageSize = 5;

        // Create a query to fetch questions
        $query = Questions::find();

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
                    'id' => $question->q_id,
                    'title' => $question->q_title,
                    'content' => $question->q_description,
                    'dateAsked' => $question->q_date,
                    'user' => [
                        'username' => $user->username,
                        'level' => $user->level
                    ]
                ];

                $total[] = $reviewData;
            }
            return [
                "status" => 200,
                "message" => "Questions retrieved successfully",
                "questions" => $total,
                "pagination" => [
                    'totalCount' => $pagination->totalCount,
                    'pageCount' => $pagination->pageCount,
                    'currentPage' => $pagination->page,
                    'pageSize' => $pagination->pageSize,
                ],
            ];
        } else {
            throw new NotFoundHttpException("No Questions Found");
        }
    }

}
?>