<?php

use app\models\Product;
use yii\base\InvalidConfigException;
use yii\db\Migration;
use yii\elasticsearch\Connection;
use yii\httpclient\Client;

/**
 * Class m241107_192518_fill_alcohol_store
 */
class m241107_192518_fill_alcohol_store extends Migration
{
    private Connection $elastic;

    public function init()
    {
        parent::init();
        $this->elastic = Yii::$app->elasticsearch;
    }

    /**
     * @return void
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function safeUp()
    {
        $categories = [
            1 => 'Вино',
            2 => 'Виски',
            3 => 'Водка',
            4 => 'Пиво',
            5 => 'Коньяк'
        ];

        $mockData = [];

        // Generate 50 test records
        for ($i = 1; $i <= 50; $i++) {
            $categoryId = array_rand($categories);

            $mockData[] = json_encode([
                'index' => [
                    '_index' => $this->getIndexName(),
                    '_type' => '_doc', // Change 'product' to '_doc'
                    '_id' => $i
                ]
            ]);

            $mockData[] = json_encode([
                'id' => $i,
                'name' => $this->generateName($categories[$categoryId]),
                'description' => $this->generateDescription($categories[$categoryId]),
                'price' => $this->generatePrice($categoryId),
                'category_id' => $categoryId,
                'image_url' => $this->generateImageUrl($i)
            ]);
        }

        // Join all data lines with newline character as required by Elasticsearch bulk API
        $bulkData = implode("\n", $mockData) . "\n";

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('http://elastic:9200/_bulk') // Set bulk endpoint
            ->addHeaders(['Content-Type' => 'application/json'])
            ->setContent($bulkData) // Use setContent for raw bulk data
            ->send();

        if ($response->isOk) {
            echo "Bulk insert was successful!";
        } else {
            echo "Bulk insert failed: " . $response->content;
            throw new Exception("Bulk insert was unsuccessful!");
        }
    }


    /**
     * @return string
     */
    public function getIndexName(): string
    {
        return Product::index();
    }

    /**
     * @param $category
     * @return string
     */
    private function generateName($category): string
    {
        $prefixes = [
            'Вино' => ['Красное', 'Белое', 'Розовое', 'Игристое'],
            'Виски' => ['Шотландский', 'Ирландский', 'Американский', 'Японский'],
            'Водка' => ['Классическая', 'Премиум', 'Особая', 'Золотая'],
            'Пиво' => ['Светлое', 'Темное', 'Нефильтрованное', 'Крафтовое'],
            'Коньяк' => ['Французский', 'Армянский', 'Российский', 'Грузинский']
        ];

        $brands = [
            'Вино' => ['Шато', 'Массандра', 'Абрау-Дюрсо', 'Мысхако'],
            'Виски' => ['Гленфиддик', 'Джемесон', 'Джек Дэниэлс', 'Хибики'],
            'Водка' => ['Белуга', 'Русский Стандарт', 'Грей Гуз', 'Финляндия'],
            'Пиво' => ['Хайнекен', 'Гиннесс', 'Бавария', 'Крафт'],
            'Коньяк' => ['Хеннесси', 'Арарат', 'Курвуазье', 'Реми Мартин']
        ];

        $prefix = $prefixes[$category][array_rand($prefixes[$category])];
        $brand = $brands[$category][array_rand($brands[$category])];

        return "$prefix $brand " . rand(1980, 2023);
    }

    /**
     * @param $category
     * @return string
     */
    private function generateDescription($category): string
    {
        $descriptions = [
            'Вино' => 'Великолепное вино с богатым букетом и послевкусием. Изготовлено из отборного винограда.',
            'Виски' => 'Премиальный виски с насыщенным вкусом и ароматом. Выдержка в дубовых бочках.',
            'Водка' => 'Кристально чистая водка высшего качества. Многоступенчатая фильтрация.',
            'Пиво' => 'Освежающее пиво с характерным вкусом и ароматом хмеля.',
            'Коньяк' => 'Изысканный коньяк с богатым вкусом и ароматом. Длительная выдержка.'
        ];

        return $descriptions[$category] . ' Крепость: ' . rand(4, 40) . '%. Объем: ' . rand(500, 1000) . 'мл.';
    }

    private function generatePrice($categoryId)
    {
        $basePrice = [
            1 => [1000, 5000],  // Вино
            2 => [2000, 8000],  // Виски
            3 => [500, 3000],   // Водка
            4 => [200, 1000],   // Пиво
            5 => [3000, 10000], // Коньяк
        ];

        return round(rand($basePrice[$categoryId][0], $basePrice[$categoryId][1]) / 100) * 100;
    }

    /**
     * @param $id
     * @return string
     */
    private function generateImageUrl($id): string
    {
        return "/images/products/alcohol_{$id}.jpg";
    }

    public function safeDown()
    {
        $this->elastic->deleteAllDocuments($this->getIndexName());
    }
}
