<?php

namespace app\controllers;

use app\models\LoginForm;
use app\models\Message;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'delete'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['delete'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return yii::$app->user->identity->role == User::ADMIN;
                        }
                    ],
                    []
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $messages = Message::find();

        /**
         * if the user is not admin don't show deleted messages
         */
        if (yii::$app->user->isGuest || yii::$app->user->identity->role == User::USER)
            $messages->where(['deleted' => false]);

        $model = new Message();
        $model->deleted = false;
        if (!yii::$app->user->isGuest && $model->load(Yii::$app->request->post()) && $model->save()) {
            /**
             * if a new message was sent, create a new clean model
             */
            $model = new Message();
            $model->deleted = false;
        }

        $messages = $messages->orderBy('created_at')->all();
        return $this->render('index', ['messages' => $messages, 'model' => $model]);
    }

    /**
     * the action to delete a message or undo it
     * @param $id
     * @return bool
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = Message::findOne($id);
        if ($model == null)
            throw new NotFoundHttpException();
        /**
         * if the message is deleted then undo it a nd vice versa
         */
        $model->deleted = !$model->deleted;
        $model->save();
        return true;
    }

    /**
     * checks if a new message was added to the database, the ajax solution is not the
     * best one to do a real time app, a websocket would be better
     * @param $count
     * @return bool
     */
    public function actionNewMessage($count)
    {
        $query = Message::find();
        if (yii::$app->user->isGuest || yii::$app->user->identity->role == \app\models\User::USER)
            $query->where(['deleted' => false]);
        return $query->count() !== $count;
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }


    /**
     * Register a new User.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new User();

        if ($model->load(Yii::$app->request->post())) {
            /**
             * log in the user after the new registration
             */
            $password = $model->password;
            if ($model->save()) {
                $login = new LoginForm();
                $login->username = $model->username;
                $login->password = $password;
                $login->rememberMe = false;
                if ($login->login())
                    return $this->goHome();
                return $this->goBack();
            }
        }

        return $this->render('register', [
            'model' => $model,
        ]);
    }
}
