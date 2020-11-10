<?php

/* @var $this yii\web\View */

use app\models\Message;
use app\models\User;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\widgets\Pjax;

/* @var $model Message */
/* @var $messages Message[] */

$this->title = 'My Yii Application';
$user = yii::$app->user;
$isAdmin = !$user->isGuest && $user->identity->role == User::ADMIN;

?>

<div class="site-index h-25">
    <div class="container">
        <?php Pjax::begin(['id' => 'messages-pj', 'enableReplaceState' => false]); ?>
        <div id="count-messages" class="hidden" data="<?= count($messages) ?>"></div>
        <div class="row">
            <div class="col-sm-12 bg-light">
                <div class="messages container overflow-auto" style="max-height: 500px">
                    <?php foreach ($messages as $message) : ?>
                        <div class="row p-1">
                            <?php $side = !$user->isGuest && $user->id === $message->user_id ? 'ml-auto' : '' ?>
                            <div class="col-sm-auto text-white px-4 rounded <?= $side ?>
                            <?= ($message->user->role == User::USER ? 'bg-dark' : 'bg-danger') ?>">
                                <div class="row">
                                    <?= \yii\helpers\Html::encode($message->content) ?>
                                </div>
                                <div class="row font-weight-lighter float-right">
                                    <?= Yii::$app->formatter->asDate($message->created_at, 'medium') ?>
                                </div>
                            </div>
                            <div class="col-sm-auto pl-1 align-self-end">
                                <?= $message->user->username ?>
                            </div>
                            <?php if ($isAdmin): ?>
                                <div class="col-sm-auto pl-1 align-self-end">
                                        <span class="btn-link btn btn-sm delete"
                                              data="<?= $message->id ?>"><?= ($message->deleted ? 'undo' : 'delete') ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php Pjax::end(); ?>
        <?php Pjax::begin(['id' => 'form-pj', 'enableReplaceState' => false]); ?>
        <?php if (!yii::$app->user->isGuest): ?>
            <div class="row">
                <div class="col-sm-12 pt-2">
                    <?php $form = ActiveForm::begin([
                        'layout' => 'inline',
                        'options' => ['data-pjax' => true],
                    ]); ?>
                    <?= $form->field($model, 'content')->textInput(['autofocus' => true])->label(false) ?>
                    <?= Html::submitButton('send', ['class' => 'btn btn-primary', 'name' => 'send-button']) ?>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        <?php endif; ?>
        <?php Pjax::end(); ?>
    </div>
</div>
