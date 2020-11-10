<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchUser */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'username',
            'role' => [
                'class' => yii\grid\DataColumn::class,
                'value' => function ($model) {
                    return $model->role == \app\models\User::ADMIN ? 'Admin' : 'User';
                }
            ],
            [
                'class' => yii\grid\ActionColumn::class,
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('Edit', ['update', 'id' => $model->id]);
                    },
                    'view' => function ($url, $model) {
                        return Html::a('View', ['view', 'id' => $model->id]);
                    },
                ],
            ],
        ],
        'pager' => [
            'class' => yii\bootstrap4\LinkPager::className(),
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
