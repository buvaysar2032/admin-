<?php

namespace api\modules\v1\controllers;

use common\models\Employees;
use yii\helpers\ArrayHelper;

class EmployeesController extends AppController
{
    public function behaviors(): array
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'auth' => ['except' => ['index']]
        ]);
    }

    public function actionIndex(): array
    {
        $positions = Employees::find()->all();

        return $this->returnSuccess([
            'positions' => $positions,
        ]);
    }
}
