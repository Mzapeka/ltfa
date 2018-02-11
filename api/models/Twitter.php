<?php
/**
 * Created by PhpStorm.
 * User: mzapeka
 * Date: 10.02.18
 * Time: 19:32
 */

namespace api\models;


use Abraham\TwitterOAuth\TwitterOAuth;
use api\exceptions\InternalErrorException;
use Yii;
use yii\helpers\ArrayHelper;


/**
 * Class Twitter
 * @package api\models
 */
class Twitter extends TwitterOAuth
{

    /**
     * @var
     */
    private $lastUserTwit;

    /**
     * Twitter constructor.
     * @throws InternalErrorException
     */
    public function __construct()
    {
        try{
            parent::__construct(
                Yii::$app->params['CONSUMER_KEY'],
                Yii::$app->params['CONSUMER_SECRET'],
                Yii::$app->params['access_token'],
                Yii::$app->params['access_token_secret']
            );
        }catch (\Exception $e){
            throw new InternalErrorException();
        }
    }


    /**
     * @return bool
     * @throws InternalErrorException
     */
    private function isSuccessRequest(){
        if($this->getLastHttpCode() != 200){
            throw new InternalErrorException();
        }
        return true;
    }

    /**
     * @param string $user
     * @return Twitter
     * @throws InternalErrorException
     */
    public function getLastUserTwit(string $user): self
    {
        $content = $this->get("search/tweets", ['q'=>'from:'.$user, 'result_type' => 'recent', 'count'=>1]);
        $this->isSuccessRequest();
        try {
            $arrayContent = ArrayHelper::toArray($content);
            $this->lastUserTwit = $arrayContent === null ? [] : $arrayContent;
            return $this;
        } catch (\Exception $e) {
            throw new InternalErrorException();
        }
    }

    /**
     * @return string
     */
    public function getTwitText():string
    {
        return ArrayHelper::getValue($this->lastUserTwit, 'statuses.0.text','');
    }

    /**
     * @return array
     */
    public function getHashtags()
    {
        $hashTagArray = ArrayHelper::getValue($this->lastUserTwit, 'statuses.0.entities.hashtags',[]);
        $result = [];
        foreach ($hashTagArray as $tag){
            $result[] = ArrayHelper::getValue($tag, 'text');
        }
        return $result;
    }


}