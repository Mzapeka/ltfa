<?php
/**
 * Created by PhpStorm.
 * User: mzapeka
 * Date: 09.02.18
 * Time: 23:52
 */

namespace api\models;


use api\exceptions\InternalErrorException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

/**
* UserList model
* @property integer $id
* @property string $request_id
* @property string $user
*
*/


class UserList extends ActiveRecord
{

    public static function tableName(): string
    {
        return '{{user_list}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user'] , 'trim'],
            [['id', 'user'], 'required'],
            [['id'], 'string', 'length'=>32],
            [['user'], 'string'],
            [['id', 'user'], 'unique', 'targetAttribute' => ['id', 'user']]
        ];
    }

    /**
     * @param string $id
     * @return ActiveDataProvider
     * @throws InternalErrorException
     */
    public static function getProviderById(string $id): ActiveDataProvider
    {
        $query = self::find()->where(['id'=>$id]);
        if(!$query->one()){
            throw new InternalErrorException();
        }
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

}