<?php

use admin\components\GroupedActionColumn;
use admin\components\widgets\detailView\ColumnImage;
use admin\components\widgets\gridView\Column;
use admin\components\widgets\gridView\ColumnSelect2;
use admin\modules\rbac\components\RbacHtml;
use admin\widgets\sortableGridView\SortableGridView;
use common\models\Positions;
use kartik\grid\SerialColumn;
use yii\widgets\ListView;

/**
 * @var $this         yii\web\View
 * @var $searchModel  common\models\EmployeesSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $model        common\models\Employees
 */

$this->title = Yii::t('app', 'Employees');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employees-index">

    <h1><?= RbacHtml::encode($this->title) ?></h1>

    <div>
        <?=
            RbacHtml::a(Yii::t('app', 'Create Employees'), ['create'], ['class' => 'btn btn-success']);
//           $this->render('_create_modal', ['model' => $model]);
        ?>
    </div>

    <?= SortableGridView::widget([
        'dataProvider' => $dataProvider,
        'pjax' => true,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::class],

            Column::widget(),
            Column::widget(['attr' => 'surname']),
            Column::widget(['attr' => 'name']),
            Column::widget(['attr' => 'patronymic']),
            ColumnSelect2::widget(['attr' => 'position_id', 'items' => Positions::findList(), 'hideSearch' => true]),
//            Column::widget(['attr' => 'workplace_number']),
//            Column::widget(['attr' => 'department']),
            ColumnImage::widget(['attr' => 'image']),
//            Column::widget(['attr' => 'x_coordinate']),
//            Column::widget(['attr' => 'y_coordinate']),

            ['class' => GroupedActionColumn::class]
        ]
    ]) ?>
</div>
