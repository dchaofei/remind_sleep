<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 18-1-25
 * Time: 上午11:43
 */

require './sms/SendMessage.php';
require './vendor/autoload.php';

use luosimao\sms\SendMessage;

$to = [
    '18339258680'
];

$text = <<<STR
测试短信
STR;

$send = new SendMessage;
echo $send->send($to, $text);