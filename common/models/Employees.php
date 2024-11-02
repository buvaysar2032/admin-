<?php

namespace common\models;

use common\components\helpers\UserUrl;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%employees}}".
 *
 * @property int $id
 * @property string|null $surname
 * @property string|null $name
 * @property string|null $patronymic
 * @property int|null $position_id
 * @property int|null $workplace_number
 * @property string|null $department
 * @property string|null $image
 * @property int|null $x_coordinate
 * @property int|null $y_coordinate
 *
 * @property-read Positions $position
 */
class Employees extends AppActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%employees}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['position_id', 'workplace_number'], 'integer'],
            [['x_coordinate', 'y_coordinate'], 'number'],
            [['surname', 'name', 'patronymic', 'department', 'image'], 'string', 'max' => 255],
            [['position_id'], 'exist', 'skipOnError' => true, 'targetClass' => Positions::class, 'targetAttribute' => ['position_id' => 'id']]
        ];
    }

    /**
     * {@inheritdoc}
     */
    final public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'surname' => Yii::t('app', 'Surname'),
            'name' => Yii::t('app', 'Name'),
            'patronymic' => Yii::t('app', 'Patronymic'),
            'position_id' => Yii::t('app', 'Position ID'),
            'workplace_number' => Yii::t('app', 'Workplace Number'),
            'department' => Yii::t('app', 'Department'),
            'image' => Yii::t('app', 'Image'),
            'x_coordinate' => Yii::t('app', 'X Coordinate'),
            'y_coordinate' => Yii::t('app', 'Y Coordinate'),
        ];
    }

    final public function getPosition(): ActiveQuery
    {
        return $this->hasOne(Positions::class, ['id' => 'position_id']);
    }

    public function fields()
    {
        return [
            'surname',
            'name',
            'patronymic',
            'position' => fn() => $this->position?->name,
            'workplace_number',
            'department',
            'image' => fn() => UserUrl::toAbsolute($this->image),
            'x_coordinate' => fn() => round($this->x_coordinate / 100, 4),
            'y_coordinate' => fn() => round($this->y_coordinate / 100, 4),
        ];
    }
}
