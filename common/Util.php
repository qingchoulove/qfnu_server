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
    public static function UUID()
    {
        return md5(uniqid());
    }

    /**
     * 发送curl请求
     * @param string
     * @param string|null
     * @param array|null
     */
    public static function Curl($url, $cookie = null, array $data = null)
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

    /**
     * 获取网络资源
     * @param string
     * @param string|null
     * @param string
     */
    public static function GetFile($url,$cookie = null)
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
    public static function RandomKey( $length = 32)
    {
        if ($length > 32 || $length < 8) {
            $length = 32;
        }
        $bytes = random_bytes($length);
        return strtr(substr(base64_encode($bytes), 0, $length), '+/', '_-');
    }

    /**
     * 生成标准学年字符串
     * @return string
     */
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
     * 获取教学周
     * @return int
     */
    public static function WeekNumber():int
    {
        $time = strtotime(Constants::START_DATE);
        return (time() - $time) / (60 * 60 * 24 * 7) + 1;
    }

    /**
     * 解析table
     * @param  string
     * @return array
     */
    public static function ParseTable($html)
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
        $tdArr = [];
        foreach ($table as $key => $tr) {
            $td = explode('{td}', $tr);
            array_pop($td);
            $td = str_replace(" ", "", $td);
            $tdArr[] = $td;
        }
        return $tdArr;
    }
}
