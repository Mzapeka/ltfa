<?php
/**
 * Created by PhpStorm.
 * User: mzapeka
 * Date: 11.02.18
 * Time: 9:50
 */

namespace api\tests\api;


use api\tests\ApiTester;
use api\tests\_data\UserFixture;
use Yii;


class ApiCest
{

    public function _fixtures(): array
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => codecept_data_dir() . 'user.php'
            ]
        ];
    }

    public function successAdd(ApiTester $I): void
    {
        $id = 'WBYX1TLPRWJ7NSV36LCPP2OZFH6AE6LM';
        $user = 'Olympics';

        $I->sendGET('/add', [
            'id' => $id,
            'user' => $user,
            'secret' => sha1($id.$user),
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseEquals('');
    }

    public function successFeed(ApiTester $I): void
    {
        $id = 'WBYX1TLPRWJ7NSV36LCPP2OZFH6AE6LM';
        $user = '';

        $I->sendGET('/feed', [
            'id' => $id,
            'secret' => sha1($id.$user),
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'user' => 'string',
            'tweet' => 'string',
            'hashtag' => 'array'
        ]);
    }

    public function successRemove(ApiTester $I): void
    {
        $id = 'WBYX1TLPRWJ7NSV36LCPP2OZFH6AE6LM';
        $user = 'elonmusk';

        $I->sendGET('/remove', [
            'id' => $id,
            'user' => $user,
            'secret' => sha1($id.$user),
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseEquals('');
    }

    public function errorAddMissingArgument(ApiTester $I): void
    {
        $id = 'WBYX1TLPRWJ7NSV36LCPP2OZFH6AE6LM';
        $user = '';

        $I->sendGET('/add', [
            'id' => $id,
            'secret' => sha1($id.$user),
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'error' => 'missing parameter'
        ]);
    }

    public function errorAddWrongSecret(ApiTester $I): void
    {
        $id = 'WBYX1TLPRWJ7NSV36LCPP2OZFH6AE6LM';
        $user = 'Olympics';

        $I->sendGET('/add', [
            'id' => $id,
            'user' => $user,
            'secret' => sha1($id.$user).'1',
        ]);

        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'error' => 'access denied'
        ]);
    }

      public function errorFeedIdNotExistInDB(ApiTester $I): void
    {
        $id = 'WBYX1TLPRWJ7NSV36LCPP2OZFH6AE6L';
        $user = '';

        $I->sendGET('/feed', [
            'id' => $id,
            'secret' => sha1($id.$user),
        ]);
        $I->seeResponseCodeIs(418);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'error' => 'internal error'
        ]);
    }
}