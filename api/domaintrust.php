<?php
// 允许所有源发起的跨域请求
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

// 从 GET 请求中获取域名参数
$domain = $_GET['domain'];

if (empty($domain)) {
    echo json_encode(['result' => "输入值不正确"]);
    die();
}

// 调用 domaindate.php 文件
require_once 'domaindate.php';

// 创建 Whois 对象
$whoisQuery = new Whois();

// 调用查询方法并获取结果
$result = $whoisQuery->query($domain);

// 解析 JSON 格式的结果
$data = json_decode($result, true);

// 获取 CreationDate 和 ExpiryDate
$creationDate = $data['main']['CreationDate'];
$expiryDate = $data['main']['ExpiryDate'];

// 提取日期部分并转换为时间戳
$creationTimestamp = strtotime(substr($creationDate, 0, 10));
$expiryTimestamp = strtotime(substr($expiryDate, 0, 10));

// 计算注册年份得分（权重60%）
$currentYear = date("Y");
$yearsSinceCreation = $currentYear - substr($creationDate, 0, 4);
$registrationScore = min(60, $yearsSinceCreation / 3 * 10); // Cap at 60%

// 计算到期时间得分（权重40%）
$remainingDaysUntilExpiry = max(0, ceil(($expiryTimestamp - time()) / (60 * 60 * 24))); // Remaining days
$expiryScore = ($remainingDaysUntilExpiry > 30) ? 40 : 0; // Cap at 40% if more than 30 days remaining

// 计算总分
$totalScore = $registrationScore + $expiryScore;

// 构建要输出的数组
$outputData = [
    'TotalScore' => round($totalScore)
];

// 输出处理后的结果
echo $totalScore;
?>

