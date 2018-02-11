<?php
namespace api\controllers;

use api\exceptions\InternalErrorException;
use api\exceptions\MissingParameterException;
use api\exceptions\WrongSecretException;
use api\models\MapDataProvider;
use api\models\Twitter;
use api\models\UserList;
use Yii;
use yii\rest\Controller;

/**
 * Site controller
 *
 */
class SiteController extends Controller
{

    /**
     * @var Twitter $twitter
     */
    private $twitter;

    /**
     * @param $action
     * @return bool
     * @throws MissingParameterException
     * @throws WrongSecretException
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if($this->action->id !== 'index'){
            $this->checkSecret();
        }
        $this->checkSecret();
        return parent::beforeAction($action);
    }


    /**
     * @return array
     */
    public function actionIndex(): array
    {
        return [
            'application' => 'Last Twit',
            'version' => '1.0.0',
        ];
    }

    /**
     * @throws InternalErrorException
     * @throws MissingParameterException
     */
    public function actionAdd(): void
    {
        $this->isUserSet();
        $model = new UserList();
        $model->attributes = Yii::$app->getRequest()->get();

        if(!$model->validate()){
            throw new InternalErrorException();
        }

        try{
            $model->save();
        }catch (\Exception $e){
            throw new InternalErrorException();
        }
        return;
    }

    /**
     * @param $id
     * @return MapDataProvider
     * @throws InternalErrorException
     */
    public function actionFeed($id)
    {
        $this->twitter = new Twitter();
        return new MapDataProvider(UserList::getProviderById($id),[$this, 'serializeLastTwits']);
    }

    /**
     * @param $id
     * @param $user
     * @throws InternalErrorException
     * @throws MissingParameterException
     * @throws \Throwable
     */
    public function actionRemove($id, $user): void
    {
        $this->isUserSet();
        if(!$model = UserList::find()->where(['id' => $id, 'user' => $user])->one()){
            throw new InternalErrorException();
        }

        try{
            $model->delete();
        }catch (\Exception $e){
            throw new InternalErrorException();
        }
        return;
    }

    /**
     * @throws MissingParameterException
     * @throws WrongSecretException
     */
    private function checkSecret(): void
    {
        $id = Yii::$app->getRequest()->get('id');
        $user = Yii::$app->getRequest()->get('user');
        $secret = Yii::$app->getRequest()->get('secret');
        if(!$secret && !$id){
            throw new MissingParameterException();
        };

        if($secret != sha1($id.$user)){
            throw new WrongSecretException();
        }
    }

    /**
     * @throws MissingParameterException
     */
    private function isUserSet(): void
    {
        $user = Yii::$app->getRequest()->get('user');
        if(!$user){
            throw new MissingParameterException();
        };
    }

    /**
     * @param UserList $user
     * @return array
     * @throws InternalErrorException
     */
    public function serializeLastTwits(UserList $user): array
    {
        $this->twitter->getLastUserTwit($user->user);
        return [
            'user' => $user->user,
            'tweet' => $this->twitter->getTwitText(),
            'hashtag' => $this->twitter->getHashtags()
        ];
    }

}
