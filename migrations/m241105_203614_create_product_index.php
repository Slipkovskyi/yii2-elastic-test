<?php

declare(strict_types=1);

use app\models\Product;
use yii\base\InvalidConfigException;
use yii\db\Migration;

/**
 * Class m241105_203614_create_product_index
 */
class m241105_203614_create_product_index extends Migration
{
    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function safeUp()
    {
        Product::createIndex();
    }

    /**
     * @return void
     * @throws InvalidConfigException
     * @throws \yii\elasticsearch\Exception
     */
    public function safeDown()
    {
        // Удаление индекса при откате миграции
        $db = Product::getDb();
        $command = $db->createCommand();
        $command->deleteIndex(Product::index());
    }
}
