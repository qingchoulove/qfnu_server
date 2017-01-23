<?php
namespace common;

class Util
{
    public static function dump($data) {
        echo "<pre>";
        var_dump($data);
        echo "</pre>";
    }

    /**
     * 生成UUID
     */
    public static function UUID(): string {
        return md5(uniqid());
    }

    /**
     * 发送curl请求
     * @param string
     * @param string|null
     * @param array|null
     */
    public static function Curl(string $url, string $cookie = null, array $data = null): string {
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 7_0 like Mac OS X; en-us) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11A465 Safari/9537.53");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT,30);
        if(!empty($data)){
            $fields_string = http_build_query($data);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        }
        if (!empty($cookie)) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}