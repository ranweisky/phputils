<?php
namespace Xiaoju\Beatles\Utils;

use Xiaoju\Beatles\Api\Config as Config;
use Xiaoju\Beatles\Utils as Utils;

class Pheanstalk {
	const LOG_TRUNCATION_LEN = 128;
	private static $instance = null;
	private static $pool = array();
	private static $key = null;
	
	const PHEANTALK_INIT_ERROR = 1;
	const PHEANTALK_SEND_MESSAGE_ERROR = 2;
	const PHEANTALK_RESERVE_ERROR = 3;

	private static $messages = array(
			self::PHEANTALK_INIT_ERROR => 'init pheanstalk failed',
			self::PHEANTALK_SEND_MESSAGE_ERROR => 'send pheanstalk message failed',
			self::PHEANTALK_RESERVE_ERROR  => 'reserve one from pheanstalk failed',
	);

	public static function getInstance(Array $config) {

		self::$key = md5(serialize($config));
		if (!isset(self::$pool[self::$key])) {
			try {
				//self::$pool = new \Pheanstalk\PheanstalkPool(Config\PheansTalk::$config[get_cfg_var('beatles.env')]);
				self::$pool[self::$key] = new \Pheanstalk\PheanstalkPool($config);
			} catch (\Exception $e) {
				Utils\Logger::fatal(
						$e->getMessage(),
						$e->getCode(),
						array('location' => __CLASS__ . '_' . __METHOD__ . '_' . __LINE__)
				);
				throw new \Exception(
						self::$messages[self::PHEANTALK_INIT_ERROR],
						self::PHEANTALK_INIT_ERROR
				);
			}
		}
		
		if (self::$instance === null) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}

	public function send($msg, $tube = 'tube1', $delay=0, $ttr=100) {

		try {
			$t1 = microtime(true);
			self::$pool[self::$key]->useTube($tube);
			$return = self::$pool[self::$key]->put($msg, 0, $delay, $ttr);
			$t2 = microtime(true);
			$duration = $t2 - $t1;
			self::logOperation('put', array('send', $tube), $duration, $return);
		} catch (\Exception $e) {
			Utils\Logger::fatal(
				$e->getMessage(),
				$e->getCode(),
				array('location' => __CLASS__ . '_' . __METHOD__ . '_' . __LINE__)
			);
			throw new \Exception(
				self::$messages[self::PHEANTALK_SEND_MESSAGE_ERROR],
				self::PHEANTALK_SEND_MESSAGE_ERROR
			);
		}
		return true;
	}

	public function reserve($tube){
		try {
			$t1 = microtime(true);
			self::$pool[self::$key]->watch($tube);
			$job = self::$pool[self::$key]->reserve(0);
			$t2 = microtime(true);			
			$duration = $t2 - $t1;
			if (is_object($job) && method_exists($job, 'getData')) {
				self::logOperation('reserve', array('reserve', $tube), $duration, $job->getData());
			} else {
				self::logOperation('reserve', array('reserve', $tube), $duration, '');
			}
		} catch (\Exception $e) {
			Utils\Logger::fatal(
				$e->getMessage(),
				$e->getCode(),
				array('location' => __CLASS__ . '_' . __METHOD__ . '_' . __LINE__)
			);
			throw new \Exception(
				self::$messages[self::PHEANTALK_RESERVE_ERROR],
				self::PHEANTALK_RESERVE_ERROR
			);
		}
        
		return $job;
	}
	
	private static function logOperation($method, $arguments, $duration, $return)
	{
		$params['cost'] = $duration;
	
		$operValue = implode(' ', $arguments);
		$length = strlen($operValue);
		if ($length > self::LOG_TRUNCATION_LEN) {
			$operValue = substr($operValue, 0, self::LOG_TRUNCATION_LEN) . '...';
		}
		$params['operName'] = $method;
		$params['operValue'] = $operValue;
		$params['operLength'] = $length;
	
		if (!empty($return)) {
			$returnValue = strval($return);
			$length = strlen($returnValue);
			if ($length > self::LOG_TRUNCATION_LEN) {
				$returnValue = substr($returnValue, 0, self::LOG_TRUNCATION_LEN) . '...';
			}
			$params['resultValue'] = $returnValue;
			$params['resultLength'] = $length;
		}
	
		if (class_exists('Xiaoju\Beatles\Utils\Logger')) {
			Logger::warning('pheanstalk operation', 0, $params);
		}
	}
}
