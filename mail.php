<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 18-1-25
 * Time: 下午1:28
 */
require __DIR__ . '/vendor/autoload.php';

$config = array_merge(
    require __DIR__ . '/config/mail_config.php',
    require __DIR__ . '/config/mail_config_local.php'
);

// 有些服务器不支持 25 端口，所以要用 ssl 方式
$transport = (new Swift_SmtpTransport($config['email_service']['domain'], 465, 'ssl'))
    ->setUsername($config['email_service']['user'])
    ->setPassword($config['email_service']['password']);

$s       = '';
$fortune = exec_command('/usr/games/fortune-zh') ?? '';

if (date('i') == 40) {
    $s = "(<b>这是最后一次提醒, 拜拜。</b>)";
}

$template = <<<STR
<!--<h3 style="text-align: center">相信我，该睡觉了</h3>-->
<div style="text-align: center;box-shadow:0px 0px 10px #000;padding: 5px 5px 5px 5px;font-size: small">
<span style="text-align: center">睡前来一首</span>
<pre>
<span style="font-size: 13px">
{$fortune}
</span>
</pre>
现在是 <b style="color: red;">%s</b>， 最迟你要在 <b style="color: green">22:40</b> 之前睡觉，因为这样对身体是最好的。Good night!{$s}
</div>
<br>
<div style="text-align: center">
<img style="box-shadow:0px 0px 10px #000; text-align: center" width="100%%" src="%s" alt="img">
</div>
STR;

$str = sprintf($template, date('H:i:s'), getImage());


// Create the Mailer using your created Transport
$mailer = new Swift_Mailer($transport);
$count  = 0;

/*
foreach ($mail_config as $to) {
    // Create a message
    $message = (new Swift_Message('Sleep Now'))
        ->setFrom(['dchaofei@163.com' => '飞哥'])
        ->setTo($to)
        ->setBody($str, 'text/html', 'utf-8');

    // Send the message
    $result = $mailer->send($message);

    if (!$result) {
        $fail .= ": 【$to】";
    }

    $count++;
}

if (isset($fail)) {
    echo print_str(' :发送失败' . $fail);
} else {
    echo print_str(' 发送成功' . $count);
}
*/

// Create a message 这样收件人会显示完。
$message = (new Swift_Message('Sleep Now'))
    ->setFrom(['dchaofei@163.com' => '飞哥'])
    ->setTo($config['to_email'])
    ->setBody($str, 'text/html', 'utf-8');

// Send the message
$result = $mailer->send($message);

if ($result) {
    echo print_str('发送成功 ' . json_encode($result));
} else {
    echo print_str('发送失败');
}

function getImage()
{
    try {
        $base_url = "https://wallhalla.com";
        $page     = rand(1, 91);
        $url      = "https://wallhalla.com/best?q=people&image=&purity=safe_sketchy&luminosity=0_100&reso=&reso_atleast=0&ratio=&order=best_trending&page=" . $page;

        $res = curlImage($url);

        $html = getHtmlInstance($res);

        $imgs = array_map(function ($value) {
            return $value->getAttr('data-src');
        }, $html->find('img'));

        preg_match('/(\w+)\.jpg/', $imgs[rand(0, count($imgs) - 1)], $matches);

        $url_img = $base_url . "/wallpaper/" . $matches[1]; // 获取到缩略图的链接

        $img_res  = curlImage($url_img);
        $img_html = getHtmlInstance($img_res);

        return $base_url . $img_html->find(".wall-img", 0)->src;

    } catch (\Exception $e) {
        return getImage();
    }

}

function curlImage($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    return curl_exec($ch);
}

function getHtmlInstance($dom)
{
    return new \HtmlParser\ParserDom($dom);
}

function print_str($str)
{
    return date('Y-m-d H:i:s') . $str . " \n";
}

/*function exec_command($cmd)
{
    $descriptorspec = [
        0 => ["pipe", "r"],
        1 => ["pipe", "w"],
        2 => ["pipe", "w"],
    ];
    $process = proc_open($cmd, $descriptorspec, $pipes);
    if (is_resource($process)) {
        fclose($pipes[0]);

        $fortune = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $error = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        proc_close($process);
    }

    $fortune = str_replace(['[33m', '[32m', '[m'], '', $fortune); //清除 fortune 输出的颜色符号

    return $fortune;
}*/

function exec_command($cmd)
{
    $fortune = shell_exec($cmd);
    return rtrim(str_replace(['[33m', '[32m', '[m'], '', $fortune), "\n"); //清除 fortune 输出的颜色符号
}