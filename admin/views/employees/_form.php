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

$this->registerCss(<<<CSS
.office-map {
    position: relative;
    width: 100%;
    height: 600px;
    background-image: url($imageUrl);
    background-size: cover;
    background-position: center;
}

.marker {
    width: 20px;
    height: 20px;
    background-color: red;
    position: absolute;
    cursor: pointer;
    border-radius: 50%;
}
CSS
);
$x = $model->x_coordinate ?: 0;
$y = $model->y_coordinate ?: 0;
$this->registerJs(<<<JS
let xCoord = $x
let yCoord = $y
const marker = $('#marker')
const markerWidth = marker[0].offsetWidth
const markerHeight = marker[0].offsetHeight
const officeMap = $('.office-map')
const officeWidth = officeMap[0].offsetWidth
const officeHeight = officeMap[0].offsetHeight
const x = $('#employees-x_coordinate')
const y = $('#employees-y_coordinate')
marker.css('left', xCoord + '%')
marker.css('top', yCoord + '%')


let isDragging = false

marker.on('mousedown', (event) => {
    isDragging = true
    event.preventDefault() // Убираем выделение текста во время перетаскивания
})
officeMap.on('mousemove', (event) => {
    if (!isDragging) {
        return
    }
    // Переводим проценты в пиксели
    xCoord = (xCoord / 100) * officeWidth
    yCoord = (yCoord / 100) * officeHeight
    // Считаем смещение
    xCoord += event.originalEvent.movementX
    yCoord += event.originalEvent.movementY
    xCoord = Math.max(0, xCoord)
    xCoord = Math.min(xCoord, officeWidth - markerWidth)
    yCoord = Math.max(0, yCoord)
    yCoord = Math.min(yCoord, officeHeight - markerHeight)
    // Возвращение маркера к курсору при возвращении в границы
    if (Math.abs(event.originalEvent.offsetX - xCoord) > markerWidth && event.originalEvent.offsetX > markerWidth) {
        xCoord = event.originalEvent.offsetX
    }
    if (Math.abs(event.originalEvent.offsetY - yCoord) > markerHeight && event.originalEvent.offsetY > markerHeight) {
        yCoord = event.originalEvent.offsetY
    }
    // Переводим пиксели в проценты
    xCoord = (xCoord / officeWidth) * 100
    yCoord = (yCoord  / officeHeight) * 100
    marker.css('left', xCoord + '%')
    marker.css('top', yCoord + '%')
    x.val(xCoord)
    y.val(yCoord)
})

document.addEventListener('mouseup', () => {
    console.log('isDragging = false')
    isDragging = false
})

JS
);
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

    <?= $form->field($model, 'x_coordinate')->label(false)->hiddenInput() ?>

    <?= $form->field($model, 'y_coordinate')->label(false)->hiddenInput() ?>

    <?php
    if ($imageUrl) {
        echo '<div class="office-map"><span class="marker" id="marker"></span></div>';
        echo '<br>';
    } else {
        echo '<p>Изображение карты не найдено.</p>';
    }
    ?>

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
