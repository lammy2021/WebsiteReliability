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

function calculateComplexity($domain) {
    // 去除 https/http
    $domain = str_replace(['https://', 'http://'], '', $domain);
    
    // 去除 www
    $domain = preg_replace('/^www\./', '', $domain);
    
    // 提取根域名
    $parts = explode('/', $domain);
    $rootDomain = $parts[0];
    
    // 计算杂乱度百分比
    $complexityPercentage = calculateComplexityPercentage($rootDomain);
    
    return $complexityPercentage;
}

function calculateComplexityPercentage($rootDomain) {
    // 杂乱度阈值，可以根据实际情况调整
    $complexityThreshold = 30;
    
    // 数字字母数字字母短间隔的权重
    $shortPatternWeight = 20;
    
    // 根域名字符数超过10的扣分设置
    $overLengthPenalty = [
        10 => 10,
        15 => 20,
        20 => 30,
        // 可根据实际情况添加更多扣分阶梯
    ];
    
    // 顶级域名是com、cn、net的加分比例
    $topLevelDomainBonus = 5; // 可根据实际情况调整
    
    // 连续数字出现5次以上的扣分权重
    $continuousNumberPenalty = 10; // 可根据实际情况调整
    
    // 获取顶级域名之前的部分
    $rootParts = explode('.', $rootDomain);
    $rootPart = $rootParts[0];
    
    // 获取域名的字符数组
    $characters = str_split($rootPart);
    
    // 统计不同字符的数量
    $uniqueCharacters = count(array_unique($characters));
    $totalCharacters = count($characters);
    
    // 计算百分比
    $complexityPercentage = ($uniqueCharacters / $totalCharacters) * 100;
    
    // 判断是否有数字字母数字字母短间隔
    $shortPatternCount = preg_match_all('/\d[a-zA-Z]\d[a-zA-Z]/', $rootPart);
    $shortPatternPercentage = ($shortPatternCount / $totalCharacters) * $shortPatternWeight;
    
    // 判断连续数字出现5次以上的情况进行扣分
    $continuousNumberCount = preg_match_all('/\d{5,}/', $rootPart);
    $continuousNumberPenaltyPercentage = ($continuousNumberCount / $totalCharacters) * $continuousNumberPenalty;
    
    // 加权计算最终百分比
    $finalPercentage = $complexityPercentage + $shortPatternPercentage - $continuousNumberPenaltyPercentage;
    
    // 根据根域名字符数进行阶梯式扣分
    foreach ($overLengthPenalty as $length => $penalty) {
        if ($totalCharacters > $length) {
            $finalPercentage -= $penalty;
        }
    }
    
    // 判断顶级域名是否是com、cn、net，是则加分
    $topLevelDomains = ['com', 'cn', 'net'];
    $tld = strtolower(pathinfo($rootDomain, PATHINFO_EXTENSION));
    if (in_array($tld, $topLevelDomains)) {
        $finalPercentage += $topLevelDomainBonus;
    }
    
    // 确保总分不超过100
    $finalPercentage = min(100, $finalPercentage);
    
    return max(0, $finalPercentage); // 确保最终百分比不小于0
}

// 获取GET请求的域名数据
if (isset($_GET['domain'])) {
    $domain = $_GET['domain'];
    
    // 计算域名复杂度百分比
    $healthPercentage = calculateComplexity($domain);
    
    // 返回数据
    echo "$healthPercentage";
} else {
    echo "Error";
}

?>
