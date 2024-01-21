<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// 检查是否存在名为 'domain' 的参数
if(isset($_GET['domain'])) {
    // 获取 'domain' 参数的值
    $domain = $_GET['domain'];

    // 构建请求URL
    $url = 'https://wx.rrbay.com/pro/wxUrlCheck.ashx?url=' . urlencode($domain);

    // 发起GET请求
    $response = file_get_contents($url);

    // 解析JSON响应
    $jsonResponse = json_decode($response, true);

    // 初始化响应数组
    $responseData = array();

    // 检查 "Msg" 字段的值
    if(isset($jsonResponse['Msg'])) {
        $msg = $jsonResponse['Msg'];

        // 根据不同的情况设置相应的返回值
        if($msg === '正常') {
            $trustValue = 100;
        } elseif($msg === '非微信官方网页，请确认是否继续访问。') {
            $trustValue = 50;
        } elseif($msg === '屏蔽') {
            $trustValue = 10;
        } else {
            // 如果 "Msg" 的值不是上述情况之一，可以设置一个默认值
            $trustValue = 0;
        }

        // 将信息添加到响应数组
        $responseData['trustValue'] = $trustValue;
        $responseData['msg'] = $msg;

        // 输出JSON格式的响应
        echo json_encode($responseData);
    } else {
        // 如果响应中不存在 "Msg" 字段
        $responseData['error'] = 'Invalid response format.';
        echo json_encode($responseData);
    }
} else {
    // 如果 'domain' 参数不存在
    $responseData['error'] = 'Please provide a domain parameter.';
    echo json_encode($responseData);
}
?>
