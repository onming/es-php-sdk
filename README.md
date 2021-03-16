# es-php-sdk
elasticsearch sdk插件
将会陆续更新

## 环境准备
*   PHP 7.2+
    您可以通过`php -v`命令查看当前的 PHP 版本。
*   curl 扩展
    您可以通过`php -m`命令查看 curl 扩展是否已经安装好。
*   安装ELK
    安装文档https://www.jianshu.com/p/1b1578af7ba0

## 安装方式 
### Composer 方式
```
composer require onming/es-php-sdk ~1.0
```

### 使用方法
```
use Onming\EsPhpSdk\Client;
$client = new Client('127.0.0.1:9200'); // 设置elasticsearch主机
$result = $client->getAnalyze('中文分词测试'); // url参数可以动态传递否则默认取当前访问链接
var_dump($signPackage);
```
