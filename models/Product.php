<?php

declare(strict_types=1);

namespace app\models;

use yii\base\InvalidConfigException;
use yii\elasticsearch\ActiveRecord;
use yii\elasticsearch\Exception;

class Product extends ActiveRecord
{
    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public static function createIndex()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->createIndex(static::index(), [
            'mappings' => static::mapping(),
        ]);
    }

    public static function index(): string
    {
        return 'alcohol_store';
    }

    public static function mapping(): array
    {
        return [
            'properties' => [
                'id' => ['type' => 'integer'],
                'name' => ['type' => 'text'],
                'description' => ['type' => 'text'],
                'price' => ['type' => 'float'],
                'category_id' => ['type' => 'integer'],
                'image_url' => ['type' => 'keyword'],
            ],
        ];
    }

    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public static function updateMapping()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->setMapping(static::index(), static::type(), static::mapping());
    }

    public static function type(): string
    {
        return '_doc';
    }

    public function attributes(): array
    {
        return ['id', 'name', 'description', 'price', 'category_id', 'image_url'];
    }
}
