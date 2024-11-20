<?php

use admin\widgets\ckfinder\CKFinderInputFile;
use admin\widgets\input\Select2;
use common\models\Param;
use common\models\Positions;
use common\widgets\AppActiveForm;
use kartik\icons\Icon;
use yii\bootstrap5\Html;
use yii\helpers\Url;

/**
 * @var $this     yii\web\View
 * @var $model    common\models\Employees
 * @var $form     AppActiveForm
 * @var $isCreate bool
 */

$imageUrl = Param::findOne(['group' => 'map', 'key' => 'image'])->value;
?>

<div class="employees-form">

    <?php $form = AppActiveForm::begin() ?>

    <?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'patronymic')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'position_id')->widget(Select2::class, ['data' => Positions::findList()]) ?>

    <?= $form->field($model, 'workplace_number')->textInput() ?>

    <?= $form->field($model, 'department')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'image')->widget(CKFinderInputFile::class) ?>

    <?= Html::tag('office-map', '', [
        'x-coordinate' => $model->x_coordinate,
        'y-coordinate' => $model->y_coordinate,
        'x-input-name' => $model->formName() . '[x_coordinate]',
        'y-input-name' => $model->formName() . '[y_coordinate]',
        'background-image' => $imageUrl
    ]) ?>

    <br>

    <div class="form-group">
        <?php if ($isCreate) {
            echo Html::submitButton(
                Icon::show('save') . Yii::t('app', 'Save And Create New'),
                ['class' => 'btn btn-success', 'formaction' => Url::to() . '?redirect=create']
            );
            echo Html::submitButton(
                Icon::show('save') . Yii::t('app', 'Save And Return To List'),
                ['class' => 'btn btn-success', 'formaction' => Url::to() . '?redirect=index']
            );
        } ?>
        <?= Html::submitButton(Icon::show('save') . Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php AppActiveForm::end() ?>

</div>
