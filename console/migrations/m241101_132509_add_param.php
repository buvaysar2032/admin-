<?php

use common\enums\ParamType;
use yii\db\Migration;

/**
 * Class m241101_132509_add_param
 */
class m241101_132509_add_param extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%param}}', [
            'group' => 'map',
            'key' => 'image',
            'description' => 'Карта офиса',
            'deletable' => 0,
            'is_active' => 1,
            'type' => ParamType::Image->value
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%param}}', [ 'group' => 'map', 'key' => 'image']);
    }
}
