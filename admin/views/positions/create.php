<?php

use common\components\helpers\UserUrl;
use common\models\PositionsSearch;
use yii\bootstrap5\Html;

/**
 * @var $this  yii\web\View
 * @var $model common\models\Positions
 */

$this->title = Yii::t('app', 'Create Positions');
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Positions'),
    'url' => UserUrl::setFilters(PositionsSearch::class)
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="positions-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', ['model' => $model, 'isCreate' => true]) ?>

</div>
