<?php

namespace Onming\EsPhpSdk;

use Exception;
use Onming\EsPhpSdk\Helper\Http;

/**
 * elasticsearch SDK
 * 
 * @author  onming <170893265@qq.com>
 * @package Onming\EsPhpSdk
 */
class Client
{
    protected $elk_host;
    protected $elk_auth;

    public function __construct($config)
    {
        $this->elk_host = $config['scheme'].'://'.$config['host'].':'.$config['port'].'/';
        if($config['user'] && $config['pass']){
            $this->elk_auth = $config['user'].':'.$config['pass'];
        }
    }

    /**
     * 创建索引
     * 
     * @param string index 索引名称
     * @param array properties 数据结构
     * @param int number_of_shards 分片数
     * @param int number_of_replicas 备份数
     * @return bool
     */
    public function createIndex($index, $properties, $number_of_shards = 1, $number_of_replicas = 0)
    {
        $params = [
            'settings' => [
                'number_of_shards' => $number_of_shards,
                'number_of_replicas' => $number_of_replicas,
            ],
            'mappings' => [
                'properties' => $properties,
            ]
        ];
        $response = $this->sendRequest($index, $params, 'PUT');
        return !empty($response['acknowledged'])?true:false;
    }

    /**
     * 获取索引配置及数据结构
     * 
     * @param string index 索引名称
     * @return array
     */
    public function getIndex($index)
    {
        return $this->sendRequest($index, '', 'GET');
    }

    /**
     * 删除索引
     * 
     * @param string index 索引名称
     * @return bool
     */
    public function deleteIndex($index)
    {
        $response = $this->sendRequest($index, '', 'DELETE');
        return !empty($response['acknowledged'])?true:false;
    }

    /**
     * 添加文档
     * 
     * @param string index 索引名称
     * @param string id 文档ID
     * @param array body 内容
     * @return string
     */
    public function insert($index, $id, $body)
    {
        $params = $body;
        $response = $this->sendRequest($index.'/_doc/'.$id, $params, 'PUT');
        return !empty($response['_id'])?$response['_id']:'';
    }

    /**
     * 删除文档
     * 
     * @param string index 索引名称
     * @param string id 文档ID
     * @return bool
     */
    public function delete($index, $id)
    {
        $response = $this->sendRequest($index.'/'.$id, '', 'DELETE');
        return $response['result']=='deleted'?true:false;
    }

    /**
     * 查询文档
     * 
     * @param string index 索引名称
     * @param array match 查询条件
     * @param int from 起始位置
     * @param int size 查询数量
     * @param int sort 排序条件
     * @return array
     */
    public function search($index, $match = '', $from = 0, $size = 100, $sort = '')
    {
        $params = [
            'query' => [],
            'from' => $from,
            'size' => $size,
        ];
        if($match == 'all'){
            $params['query']['match_all'] = new \stdClass();
        }else{
            $params['query'] = $match;
        }
        if($sort){
            $params['sort'] = $sort;
        }
        $response = $this->sendRequest($index.'/_search', $params, 'POST');
        $result = ['total' => 0, 'list' => []];
        if(!empty($response['hits'])){
            return ['total' => $response['hits']['total']['value'], 'list' => $response['hits']['hits']];
        }
        return $result;
    }

    /**
     * 中文分词
     * 
     * @param  string words 关键词
     * @return array
     */
    public function getAnalyze($words)
    {
        $params = [
            "analyzer" => "ik_max_word",
            "text" => $words
        ];
        return $this->sendRequest('_analyze', $params, 'POST');
    }

    protected function sendRequest($url, $params, $method)
    {
        $http_header[] = 'Content-Type: application/json';
        if($this->elk_auth){
            $http_header[] = 'Authorization: Basic '.base64_encode($this->elk_auth);
        }
        $options = [CURLOPT_HTTPHEADER => $http_header];
        if($params){
            $params = json_encode($params);
        }
        $response = Http::sendRequest($this->elk_host.$url, $params, $method, $options);
        if($response['ret']){
            return json_decode($response['msg'], true);
        }else{
            throw new Exception($response['msg']);
        }
    }

}
