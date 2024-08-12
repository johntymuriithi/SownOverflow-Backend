<?php
namespace app\controllers;

use app\models\Answers;

use app\models\Categories;
use app\models\Orders;
use app\models\Questions;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class CategoriesController extends BaseController
{
    public $modelClass = 'app\models\Categories';


    public function actionCategoryadd()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->bodyParams;
        $category = new Categories();
        $category->category_name = Yii::$app->request->post('category_name');

        if ($category->save()) {
            return ['status' => 200, 'message' => 'Category Added Successfully'];
        } else {
            return ['status' => false, 'errors' => $category->errors];
        }
    }

    public function actionShowcategory()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $category = Categories::find()->all();
        if ($category) {
            return $category;
        } else {
            throw new NotFoundHttpException("No Categories for now!");
        }
    }

}
?>