<?php
/**
 * Created by PhpStorm.
 * User: mzapeka
 * Date: 10.02.18
 * Time: 14:34
 */

namespace api\exceptions;


use yii\web\HttpException;

class MissingParameterException extends HttpException
{
    public function __construct()
    {
        parent::__construct(400, 'missing parameter');
    }
}