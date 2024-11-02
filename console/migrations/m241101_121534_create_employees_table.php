<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%employees}}`.
 */
class m241101_121534_create_employees_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    final public function safeUp()
    {
        $this->createTable('{{%employees}}', [
            'id' => $this->primaryKey(),
            'surname' => $this->string(),
            'name' => $this->string(),
            'patronymic' => $this->string(),
            'position_id' => $this->integer(),
            'workplace_number' => $this->integer(),
            'department' => $this->string(),
            'image' => $this->string(),
            'x_coordinate' => $this->integer(),
            'y_coordinate' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-employees-position_id',
            '{{%employees}}',
            'position_id',
            '{{%positions}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    final public function safeDown()
    {
        $this->dropForeignKey('fk-employees-position_id', '{{%employees}}');
        $this->dropTable('{{%employees}}');
    }
}
