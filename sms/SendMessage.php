<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 18-1-25
 * Time: 上午11:25
 */

namespace luosimao\sms;

use GuzzleHttp\Client;

class SendMessage
{
    public $apiKey = '42d183775c8c7c012505b3c493983118';
    public $apiUrl = 'http://sms-api.luosimao.com/v1/send.json';
    public $apiBatchUrl = 'http://sms-api.luosimao.com/v1/send_batch.json';

    public $signName = '铁壳测试';

    public function send(array $to, $text)
    {
        $to = implode(',', $to);

        $client = new Client();
        // 批量发送
        if (strpos($to, ',')) {
            $response = $client->request('POST', $this->apiBatchUrl, [
                'auth'        => ['api', 'key-' . $this->apiKey],
                'form_params' => [
                    'mobile_list' => $to,
                    'message'     => $text . '【' . $this->signName . '】',
                ],
            ]);
        } else {
            $response = $client->request('POST', $this->apiUrl, [
                'auth'        => ['api', 'key-' . $this->apiKey],
                'form_params' => [
                    'mobile'  => $to,
                    'message' => $text . '【' . $this->signName . '】',
                ],
            ]);
        }

        if ($response->getStatusCode() == 200) {
            $raw = json_decode($response->getBody(), true);

            if ($raw['error'] != 0) {
                return $this->print_f('发送失败, 错误码为： ' . "【{$raw['error']}】");
            }

            return $this->print_f('发送成功');
        } else {
            return $this->print_f('发送失败' . \GuzzleHttp\json_encode($response));
        }
    }

    private function print_f($str)
    {
        return date('Y-m-d H:i:s') . ": " . $str . "\n";
    }
}
