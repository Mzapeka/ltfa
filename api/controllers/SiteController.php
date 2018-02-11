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
 * @property Twitter $twitter
 */
class SiteController extends Controller
{

    private $twitter;

    public function beforeAction($action)
    {
        $this->checkSecret();
        return parent::beforeAction($action);
    }

    /**
     * @SWG\Get(
     *     path="/",
     *     tags={"Info"},
     *     @SWG\Response(
     *         response="200",
     *         description="API version",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="version", type="string")
     *         ),
     *     )
     * )
     */
    public function actionIndex(): array
    {
        return [
            'version' => '1.0.0',
        ];
    }
    
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

    public function actionFeed($id)
    {
        $this->twitter = new Twitter();
        return new MapDataProvider(UserList::getProviderById($id),[$this, 'serializeLastTwits']);
    }

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

    private function isUserSet(): void
    {
        $user = Yii::$app->getRequest()->get('user');
        if(!$user){
            throw new MissingParameterException();
        };
    }

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
