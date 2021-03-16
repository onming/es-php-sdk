<?php

namespace Onming\EsPhpSdk;

use Onming\EsPhpSdk\Helper\Http;

/**
 * elasticsearch SDK
 * 
 * @author  onming <170893265@qq.com>
 * @package Onming\EsPhpSdk
 */
class Client
{
    private $elk_host;

    public function __construct($host)
    {
        $this->elk_host = "http://{$host}/";
    }

    /**
     * 中文分词
     */
    public function getAnalyze($words)
    {
        $data = [
            "analyzer" => "ik_max_word",
            "text" => $words
        ];
        $result = Http::post($this->elk_host.'_analyze', json_encode($data));
        return json_decode($result, true);
    }

}
