<?php

use common\components\helpers\UserUrl;
use common\models\EmployeesSearch;
use yii\bootstrap5\Html;

/**
 * @var $this  yii\web\View
 * @var $model common\models\Employees
 */

$this->title = Yii::t('app', 'Create Employees');
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Employees'),
    'url' => UserUrl::setFilters(EmployeesSearch::class)
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employees-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', ['model' => $model, 'isCreate' => true]) ?>

</div>
