<?php

use admin\components\widgets\detailView\Column;
use admin\components\widgets\detailView\ColumnImage;
use admin\components\widgets\gridView\ColumnSelect2;
use admin\modules\rbac\components\RbacHtml;
use common\components\helpers\UserUrl;
use common\models\EmployeesSearch;
use common\models\Positions;
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var $this  yii\web\View
 * @var $model common\models\Employees
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Employees'),
    'url' => UserUrl::setFilters(EmployeesSearch::class)
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employees-view">

    <h1><?= RbacHtml::encode($this->title) ?></h1>

    <p>
        <?= RbacHtml::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= RbacHtml::a(
            Yii::t('app', 'Delete'),
            ['delete', 'id' => $model->id],
            [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post'
                ]
            ]
        ) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            Column::widget(),
            Column::widget(['attr' => 'surname']),
            Column::widget(['attr' => 'name']),
            Column::widget(['attr' => 'patronymic']),
            ColumnSelect2::widget(['attr' => 'position_id', 'items' => Positions::findList(), 'hideSearch' => true]),
            Column::widget(['attr' => 'workplace_number']),
            Column::widget(['attr' => 'department']),
            ColumnImage::widget(['attr' => 'image']),
            Column::widget(['attr' => 'x_coordinate']),
            Column::widget(['attr' => 'y_coordinate']),
        ]
    ]) ?>

</div>
