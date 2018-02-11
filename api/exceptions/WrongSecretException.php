<?php
/**
 * Created by PhpStorm.
 * User: mzapeka
 * Date: 10.02.18
 * Time: 10:02
 */

namespace api\exceptions;


use yii\web\HttpException;
use yii\web\Response;

class WrongSecretException extends HttpException
{
    public function __construct()
    {
        parent::__construct(403, 'access denied');
    }
}