<?php
header('Access-Control-Allow-Origin: *');//允许异步请求，可以把 * 设置为固定域名
header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");//请求方式
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");//允许接收的header头参数 例如:需要传参userid，则需要这里配置一下
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    header('Access-Control-Allow-Origin: *');//与上面的同理
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");//与上面的同理
    header("HTTP/1.1 200 OK");
    die();
}

// 获取传递的域名参数
$domainToQuery = $_GET['domain'] ?? '';

// 检查是否提供了域名
if (empty($domainToQuery)) {
    echo "请提供域名参数";
    exit;
}

// 设置请求的 URL
$url = '';//这里填写你的API地址，例如：http://api.yourdomain.com/regcheck.php

// 构建完整的请求 URL
$requestUrl = $url . '?domain=' . urlencode($domainToQuery);

// 发起 GET 请求并获取结果
$response = file_get_contents($requestUrl);

// 解析 JSON 数据
$data = json_decode($response, true);

// 检查是否包含 RegistrationScore
if (isset($data['unregistered'])) {
    echo "0"; // 包含 RegistrationScore，显示 1
} else {
    echo "1"; // 不包含 RegistrationScore，显示 0
}
?>
