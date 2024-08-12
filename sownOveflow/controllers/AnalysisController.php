<?php
namespace app\controllers;

use app\models\Answers;
use app\models\Categories;
use app\models\Questions;
use app\models\User;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AnalysisController extends BaseController
{
    public $modelClass = 'app\models\Categories';

    public function actionGetquestions($categoryname)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Find the category by name
        $category = Categories::find()->where(['category_name' => $categoryname])->one();
        if (!$category) {
            throw new NotFoundHttpException("Category not found");
        }

        // Pagination parameters
        $page = Yii::$app->request->get('page', 1); // Default to page 1
        $limit = Yii::$app->request->get('limit', 4); // Default to 4 items per page

        // Validate page and limit
        $page = max(1, (int)$page);
        $limit = max(1, (int)$limit);

        // Calculate offset
        $offset = ($page - 1) * $limit;

        // Find questions belonging to the category with pagination
        $query = Questions::find()
            ->where(['category_id' => $category->category_id])
            ->with('answers') // Eager load related answers
            ->offset($offset)
            ->limit($limit);

        $questions = $query->all();

        if ($questions) {
            $totalCount = Questions::find()->where(['category_id' => $category->category_id])->count();
            $totalPages = ceil($totalCount / $limit);

            $response = [];
            foreach ($questions as $question) {
                $user = User::findOne(['id' => $question->user_id]);
                $response[] = [
                    'id' => $question->q_id,
                    'title' => $question->q_title,
                    'content' => $question->q_description,
                    'votes' => $question->q_votes,
                    'dateAsked' => $question->q_date,
                    'user' => [
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
                                'username' => $user->username,
                                'level' => $user->level
                            ]
                        ];
                    }, $question->answers),
                ];
            }

            return [
                "status" => 200,
                "message" => "Questions retrieved successfully",
                "questions" => $response,
                "pagination" => [
                    "currentPage" => $page,
                    "totalPages" => $totalPages,
                    "totalCount" => $totalCount,
                    "pageSize" => $limit,
                ],
            ];
        } else {
            throw new NotFoundHttpException("Questions for the category $categoryname not found");
        }
    }

    public function actionGetquestionsbyuser()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = Yii::$app->user->id;

        // Pagination parameters
        $page = Yii::$app->request->get('page', 1); // Default to page 1
        $limit = Yii::$app->request->get('limit', 4); // Default to 4 items per page

        // Validate page and limit
        $page = max(1, (int)$page);
        $limit = max(1, (int)$limit);

        // Calculate offset
        $offset = ($page - 1) * $limit;

        // Find questions by user with pagination
        $query = Questions::find()
            ->where(['user_id' => $userId])
            ->with('answers') // Eager load related answers
            ->offset($offset)
            ->limit($limit);

        $questions = $query->all();

        if ($questions) {
            $totalCount = Questions::find()->where(['user_id' => $userId])->count();
            $totalPages = ceil($totalCount / $limit);

            $response = [];
            foreach ($questions as $question) {
                $user = User::findOne(['id' => $question->user_id]);
                $response[] = [
                    'id' => $question->q_id,
                    'title' => $question->q_title,
                    'content' => $question->q_description,
                    'dateAsked' => $question->q_date,
                    'user' => [
                        'username' => $user->username,
                        'level' => $user->level
                    ],
                    'answers' => array_map(function($answer) {
                        $user = User::findOne(['id' => $answer->user_id]);
                        return [
                            'id' => $answer->a_id,
                            'content' => $answer->a_description,
                            'dateAnswered' => $answer->a_date,
                            'user' => [
                                'username' => $user->username,
                                'level' => $user->level
                            ]
                        ];
                    }, $question->answers),
                ];
            }

            return [
                "status" => 200,
                "message" => "Questions retrieved successfully",
                "questions" => $response,
                "pagination" => [
                    "currentPage" => $page,
                    "totalPages" => $totalPages,
                    "totalCount" => $totalCount,
                    "pageSize" => $limit,
                ],
            ];
        } else {
            throw new NotFoundHttpException("No questions found for the user with ID $userId");
        }
    }

    public function actionGetquestionsansweredbyuser()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = Yii::$app->user->id;

        // Pagination parameters
        $page = Yii::$app->request->get('page', 1); // Default to page 1
        $limit = Yii::$app->request->get('limit', 4); // Default to 4 items per page

        // Validate page and limit
        $page = max(1, (int)$page);
        $limit = max(1, (int)$limit);

        // Calculate offset
        $offset = ($page - 1) * $limit;

        // Find question IDs that the user has answered
        $questionIds = Answers::find()
            ->select('q_id')
            ->where(['user_id' => $userId])
            ->distinct()
            ->column();

        if (empty($questionIds)) {
            throw new NotFoundHttpException("No questions found that the user with ID $userId has answered.");
        }

        // Find questions by IDs with pagination
        $query = Questions::find()
            ->where(['q_id' => $questionIds])
            ->with('answers') // Eager load related answers
            ->offset($offset)
            ->limit($limit);

        $questions = $query->all();

        if ($questions) {
            $totalCount = Questions::find()
                ->where(['q_id' => $questionIds])
                ->count();
            $totalPages = ceil($totalCount / $limit);

            $response = [];
            foreach ($questions as $question) {
                $user = User::findOne(['id' => $question->user_id]);
                $response[] = [
                    'id' => $question->q_id,
                    'title' => $question->q_title,
                    'content' => $question->q_description,
                    'dateAsked' => $question->q_date,
                    'user' => [
                        'username' => $user->username,
                        'level' => $user->level
                    ],
                    'answers' => array_map(function($answer) {
                        $user = User::findOne(['id' => $answer->user_id]);
                        return [
                            'id' => $answer->a_id,
                            'content' => $answer->a_description,
                            'dateAnswered' => $answer->a_date,
                            'user' => [
                                'username' => $user->username,
                                'level' => $user->level
                            ]
                        ];
                    }, $question->answers),
                ];
            }

            return [
                "status" => 200,
                "message" => "Questions retrieved successfully",
                "questions" => $response,
                "pagination" => [
                    "currentPage" => $page,
                    "totalPages" => $totalPages,
                    "totalCount" => $totalCount,
                    "pageSize" => $limit,
                ],
            ];
        } else {
            throw new NotFoundHttpException("No questions found that the user with ID $userId has answered.");
        }
    }
}

?>