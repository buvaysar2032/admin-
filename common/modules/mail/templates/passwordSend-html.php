<?php

/**
 * @var $this    yii\web\View
 * @var $message common\modules\mail\components\Message
 * @var $user    common\modules\user\models\User
 * @var $data    array
 */

$data = $data ?? [];
if (empty($data['password'])) {
    $data['password'] = 'not_set';
}
$this->registerCss(file_get_contents(__DIR__ . '/' . str_replace('-html.php', '.css', basename($this->viewFile))));
echo $this->render(str_replace('-html.php', '.pug', basename($this->viewFile)), $data);