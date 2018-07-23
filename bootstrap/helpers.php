<?php

function flash($status = 'success', $msg = '操作成功', $key = 'toastrMsg')
{
    session()->flash($key, ['status' => $status, 'message' => $msg]);
}

function admin_log_record($user_id, $type, $table_name, $content_message = '', $content_data = '')
{
    return (new \App\Models\Log())->storeLog([
        'user_id' => $user_id,
        'type' => $type,
        'table_name' => $table_name,
        'ip' => get_client_ip(),
        'content' => [
            'data' => $content_data,
            'message' => $content_message,
        ]
    ]);
}

/**
 * 获取客户端 ip
 * @return array|false|null|string
 */
function get_client_ip()
{
    static $realip = NULL;
    if ($realip !== NULL) {
        return $realip;
    }
    //判断服务器是否允许$_SERVER
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $realip = $_SERVER['REMOTE_ADDR'];
        }
    } else {
        //不允许就使用getenv获取
        if (getenv("HTTP_X_FORWARDED_FOR")) {
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
            $realip = getenv("HTTP_CLIENT_IP");
        } else {
            $realip = getenv("REMOTE_ADDR");
        }
    }

    return $realip;
}

/**
 * 判断数组的键是否存在，并且佱不为空
 * @param $arr
 * @param $column
 * @return null
 */
function isset_and_not_empty($arr, $column)
{
    return (isset($arr[$column]) && $arr[$column]) ? $arr[$column] : '';
}

/**
 * 过滤用户输入数据
 * @param $str
 * @return mixed
 *
 */
function trimall($str)
{
    $qian = array(" ", "　", "\t", "\n", "\r");
    $qian = array(" ", "　", "\t");
    $hou = array("", "", "");
    return str_replace($qian, $hou, $str);
}

/**
 * 将时间戳转换成 xx 时\xx 分
 * @param $time
 * @return array
 */
function get_hour_and_min($time)
{
    $sec = round($time / 60);
    if ($sec >= 60) {
        $hour = floor($sec / 60);
        $min = $sec % 60;

    } else {
        $hour = 0;
        $min = $sec;
    }
    return ['hour' => $hour, 'min' => $min];
}

/**
 * 根据经纬度获取两点间的直线距离，返回 KM
 * @param $lon1
 * @param $lat1
 * @param $lon2
 * @param $lat2
 * @return float
 */
function get_two_position_distance($lon1, $lat1, $lon2, $lat2)
{
    $radius = 6378.137;
    $rad = floatval(M_PI / 180.0);

    $lat1 = floatval($lat1) * $rad;
    $lon1 = floatval($lon1) * $rad;
    $lat2 = floatval($lat2) * $rad;
    $lon2 = floatval($lon2) * $rad;

    $theta = $lon2 - $lon1;

    $dist = acos(sin($lat1) * sin($lat2) +
        cos($lat1) * cos($lat2) * cos($theta)
    );

    if ($dist < 0) {
        $dist += M_PI;
    }

    return round($dist * $radius, 3);
}
