<?php
namespace common;

class Util
{
    public static function Dump($data)
    {
        echo "<pre>";
        var_dump($data);
        echo "</pre>";
    }

    /**
     * 生成UUID
     */
    public static function UUID():string
    {
        return md5(uniqid());
    }

    /**
     * 发送curl请求
     * @param string
     * @param string|null
     * @param array|null
     */
    public static function Curl(string $url, string $cookie = null, array $data = null):string
    {
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 7_0 like Mac OS X; en-us) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11A465 Safari/9537.53");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if (!empty($data)) {
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
        if ($output == null) {
            return '';
        }
        return $output;
    }

    public static function GetFile(string $url, string $cookie = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 7_0 like Mac OS X; en-us) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11A465 Safari/9537.53");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if (!empty($cookie)) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    /**
     * 生成指定长度随机Key
     * @param integer $length [description]
     */
    public static function RandomKey(int $length = 32):string
    {
        if ($length > 32 || $length < 8) {
            $length = 32;
        }
        if (function_exists('random_bytes')) {
            return bin2hex(random_bytes($length));
        }
        if (function_exists('mcrypt_create_iv')) {
            return bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
        }
        if (function_exists('openssl_random_pseudo_bytes')) {
            return bin2hex(openssl_random_pseudo_bytes($length));
        }
    }

    public static function SchoolYear():string
    {
        $month = date("m");
        if ($month < 8) {
            $year = date('Y', strtotime('-1 year')) . '-' . date('Y') . '-2-1';
        } else {
            $year = date('Y') . '-' . date('Y', strtotime('+1 year')) . '-1-1';
        }
        return $year;
    }

    /**
     * 解析table
     * @param  string
     * @return array
     */
    public static function ParseTable(string $html):array
    {

        $table = preg_replace("'<tr[^>]*?>'si", "", $html);
        $table = preg_replace("'<td[^>]*?>'si", "", $table);
        $table = str_replace("</tr>", "{tr}", $table);
        $table = str_replace("</td>", "{td}", $table);
        //去 HTML 标记
        $table = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $table);
        $table=str_replace("&nbsp;", "", $table);
        //去空白字符
        $table = preg_replace("'([\r\n])[\s]+'", "", $table);
        $table = str_replace(" ", "", $table);
        $table = str_replace(" ", "", $table);
        $table = explode('{tr}', $table);
        array_pop($table);
        foreach ($table as $key => $tr) {
            $td = explode('{td}', $tr);
            array_pop($td);
            $td = str_replace(" ", "", $td);
            $tdArr[] = $td;
        }
        return $tdArr;
    }
}
