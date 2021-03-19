# es-php-sdk
elasticsearch sdk插件
将会陆续更新

## 环境准备
*   PHP 7.0+
    您可以通过`php -v`命令查看当前的 PHP 版本。
*   curl 扩展
    您可以通过`php -m`命令查看 curl 扩展是否已经安装好。
*   安装ELK 6.0+
    安装文档https://www.jianshu.com/p/1b1578af7ba0

## 安装方式 
### Composer 方式
```
composer require onming/es-php-sdk ~1.0
```

### 使用方法
```
use Onming\EsPhpSdk\Client;

$hosts = [
    'host' => '127.0.0.1',
    'port' => '9200',
    'scheme' => 'http',
    'user' => '',
    'pass' => '',
];
$client = new Client($hosts);
// 测试Analyze
$result = $client->getAnalyze('测试中文分词');
var_dump($result);
// 删除索引
$result = $client->deleteIndex('test_index');
var_dump($result);
// 创建索引
// +----------------------------------------------------------------------
// | 类型参考
// | text:全文检索，分词
// | keyword:精确查询不分词
// | Intger:整形
// | float:浮点
// | nested:存储array或json
// | date:日期类型 格式化 yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis
// | geo_point:用来存储经纬度
// | ip:可以存储IPV4 和IPV6
// +----------------------------------------------------------------------
$properties = [
    'md5' => [
        'type' => 'keyword',
    ],
    'tag' => [
        'type' => 'text',
        'analyzer' => 'ik_max_word',
        'index' => true,
        'store' => false,
    ],
    'scale' => [
        'type' => 'float',
    ],
    'position' => [
        'type' => 'geo_point',
    ],
    'caeate_at' => [
        'type' => 'date',
        'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis',
    ],
];
$result = $client->createIndex('test_index', 'test_type', $properties);
var_dump($result);
// 添加文档
$body = [
    'md5' => '111111',
    'tag' => '狗,宠物,动物,户外,狗狗,哺乳动物,毛茸茸,雪,可爱,自然',
    'scale' => 1.1,
    'position' => ['lon' => 116.111, 'lat' => 39.111],
    'create_at' => date('Y-m-d H:i:s'),
];
$result = $client->insert('test_index', 'test_type', '1', $body);
var_dump($result);
$body = [
    'md5' => '222222',
    'tag' => '老虎,宠物,动物,户外,狗狗,哺乳动物,毛茸茸,雪,可爱,自然',
    'scale' => 0.8,
    'position' => ['lon' => 116.222, 'lat' => 39.222],
    'create_at' => date('Y-m-d H:i:s'),
];
$result = $client->insert('test_index', 'test_type', '2', $body);
var_dump($result);
// 删除文档
$result = $client->delete('test_index', 'test_type', '1');
var_dump($result);
// 查询所有
$result = $client->search('test_index', 'test_type', 'all');
// 按条件查询排序
$match = ['tag' => '宠物狗'];
$sort = ['_score' => 'desc'];
$result = $client->search('test_index', 'test_type', $match, 0, 100, $sort);
var_dump($result);
```
