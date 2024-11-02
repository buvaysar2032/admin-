<?php

namespace common\models;

use common\models\AppActiveRecord;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%positions}}".
 *
 * @property int              $id
 * @property string|null      $name
 *
 * @property-read Employees[] $employees
 */
class Positions extends AppActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%positions}}';
    }

    public static function findList()
    {
        return ArrayHelper::map(self::find()->all(), 'id', 'name');
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * {@inheritdoc}
     */
    final public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    final public function getEmployees(): ActiveQuery
    {
        return $this->hasMany(Employees::class, ['position_id' => 'id']);
    }
}
