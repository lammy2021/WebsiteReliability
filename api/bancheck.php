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

function getBanCount($domain) {
    // 读取 ban.json 文件内容
    $banData = file_get_contents('ban.json');

    // 解析 JSON 数据
    $banList = json_decode($banData, true);

    // 检查是否存在指定域名
    if (isset($banList[$domain])) {
        // 如果存在，返回对应的数目
        return $banList[$domain];
    } else {
        // 如果不存在，返回 0
        return 0;
    }
}

// 你可以调用这个函数并传入要检查的域名
$domainToCheck = $_GET['domain'];
$result = getBanCount($domainToCheck);

// 输出结果
echo "$result";

// 如果你希望每次都重新读取 ban.json 文件，可以在每次查询之前清除缓存
clearstatcache();
?>
