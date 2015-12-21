<?php
namespace Xiaoju\Beatles\Utils;

use DrSlump\Protobuf\Exception;
class ThriftWrapper {
    
    const LOG_TRUNCATION_LEN = 256;
    
    private $client = null;
    private $tag = '';
    
    public function __construct($client, $tag = '') {
        $this->client = $client;
        $this->tag = $tag;
    }
    
    public function __call($method, $arguments) {
        
        if (!method_exists($this->client, $method)) {
            throw new \BadFunctionCallException('thrift client method not support: ' . $method);
        }
        $t1 = microtime(true);
        try {
            $return = call_user_func_array(array($this->client, $method), $arguments);
        } catch (\Exception $e) {
            $t2 = microtime(true);
            $duration = $t2 - $t1;
            
            $errMsg = $e->getMessage();
            $errCode = $e->getCode();
            $this->logOperation('thrift_exception_' . $this->tag . '_' . $method, $arguments, $duration, $errCode . '_' . $errMsg);
            throw new \Exception($errMsg, $errCode);
        }
        
        $t2 = microtime(true);
        $duration = $t2 - $t1;
        $this->logOperation($method, $arguments, $duration, $return);
        return $return;
    }
    
    /**
     * @param $method
     * @param $arguments
     * @param $duration
     * @param $return
     */
    private function logOperation($method, $arguments, $duration, $return)
    {
        $params['cost'] = $duration;
    
        $operValue = json_encode($arguments);
        $length = strlen($operValue);
        if ($length > self::LOG_TRUNCATION_LEN) {
            $operValue = substr($operValue, 0, self::LOG_TRUNCATION_LEN) . '...';
        }
        
        $params['operName'] = $method;
        $params['operValue'] = $operValue;
        $params['operLength'] = $length;
    
        if (!empty($return)) {
            $returnValue = json_encode($return);
            $length = strlen($returnValue);
            if ($length > self::LOG_TRUNCATION_LEN) {
                $returnValue = substr($returnValue, 0, self::LOG_TRUNCATION_LEN) . '...';
            }
            $params['resultValue'] = $returnValue;
            $params['resultLength'] = $length;
        }
    
        Logger::warning('thrift_operation_' . $this->tag . '_trace', 0, $params);
    }
}
