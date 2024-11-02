<?php

use yii\db\Migration;

/**
 * Class m241102_111745_edit_employees_table
 */
class m241102_111745_edit_employees_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%employees}}', 'x_coordinate', $this->decimal(5,2));
        $this->alterColumn('{{%employees}}', 'y_coordinate', $this->decimal(5, 2));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%employees}}', 'x_coordinate', $this->integer());
        $this->alterColumn('{{%employees}}', 'y_coordinate', $this->integer());
    }
}
