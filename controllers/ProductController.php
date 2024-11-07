<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\Product;
use yii\elasticsearch\ActiveDataProvider;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;

class ProductController extends Controller
{
    /**
     * Lists all products.
     * @return ActiveDataProvider
     */
    public function actionIndex(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Product::find(),
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
    }

    public function actionFullTextSearch(string $searchTerm): array
    {
        return Product::find()
            ->query(
                [
                    'query_string' => [
                        'query' => $searchTerm,
                        'fields' => ['*'],
                        'default_operator' => 'AND'
                    ]
                ])->all();
    }

    /**
     * Displays a single product.
     * @param int $id
     * @return Product
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): Product
    {
        return $this->findModel($id);
    }

    /**
     * Finds the Product model based on its primary key value.
     * @param int $id
     * @return Product
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): Product
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested product does not exist.');
    }
}
