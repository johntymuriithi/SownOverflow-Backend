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
        $questions = Questions::find()
            ->where(['category_id' => $category->category_id])
            ->with('answers', 'categories')->all();

        if ($questions) {

            $response = [];
            foreach ($questions as $question) {
                $user = User::findOne(['id' => $question->user_id]);
                $response[] = [
                    'id' => $question->q_id,
                    'title' => $question->q_title,
                    'content' => $question->q_description,
                    'votes' => $question->q_votes,
                    'dateAsked' => $question->q_date,
                    "category_id"  => $question->category_id,
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
            }

            return [
                "status" => 200,
                "message" => "Questions retrieved successfully",
                "questions" => $response,
            ];
        } else {
            throw new NotFoundHttpException("Questions for the category $categoryname not found");
        }
    }

    public function actionGetquestionsbyuser()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = Yii::$app->user->id;


        // Find questions by user with pagination
        $questions= Questions::find()
            ->where(['user_id' => $userId])
            ->with('answers', 'categories')->all();

        if ($questions) {
            $response = [];
            foreach ($questions as $question) {
                $user = User::findOne(['id' => $question->user_id]);
                $response[] = [
                    'id' => $question->q_id,
                    'title' => $question->q_title,
                    'content' => $question->q_description,
                    'dateAsked' => $question->q_date,
                    "category_id"  => $question->category_id,
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
                            'dateAnswered' => $answer->a_date,
                            'user' => [
                                'id' => $user->id,
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
            ];
        } else {
            throw new NotFoundHttpException("No questions found for the user with ID $userId");
        }
    }

    public function actionGetquestionsansweredbyuser()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = Yii::$app->user->id;

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
        $questions = Questions::find()
            ->where(['q_id' => $questionIds])
            ->with('answers', 'categories')->all();

        if ($questions) {
            $response = [];
            foreach ($questions as $question) {
                $user = User::findOne(['id' => $question->user_id]);
                $response[] = [
                    'id' => $question->q_id,
                    'title' => $question->q_title,
                    'content' => $question->q_description,
                    'dateAsked' => $question->q_date,
                    "category_id"  => $question->category_id,
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
                            'dateAnswered' => $answer->a_date,
                            'user' => [
                                'id' => $user->id,
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
            ];
        } else {
            throw new NotFoundHttpException("No questions found that the user with ID $userId has answered.");
        }
    }

    public function actionSiteinfo() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $users = User::find()->count();
        $posts = Questions::find()->count();

        return ["users" => $users, "questions" => $posts];
    }
}

?>