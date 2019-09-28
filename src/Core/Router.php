<?php
/**
 * Created by PhpStorm.
 * User: laiconglin
 * Date: 26/11/2017
 * Time: 21:19
 */

namespace Koala\Core;

use Koala\Core\Exception\ErrorCode;
use Koala\Core\Exception\RequestException;
use Koala\Log\Logger;
use Koala\Config\ConfigPool;

class Router
{
	public $module = 'index';
	public $controller = 'index';
	public $action = 'index';

	public $requestUri = '/';

	private static $routerConfig = [];
	protected static function initRouteConfig() {
		if (!empty(self::$routerConfig)) {
			return;
		}
		self::$routerConfig = ConfigPool::getConfig('routes');
	}

	public function __construct($requestUri = '/')
	{
		// load routers config
		self::initRouteConfig();
		$curLogger = Logger::getLogger('app');
		// Loop through the route array looking for wildcards
		if (is_array(self::$routerConfig) && !empty(self::$routerConfig)) {
			foreach (self::$routerConfig as $key => $val) {
				// Convert wildcards to RegEx
				$key = str_replace(array(':any', ':num', '/'), array('[^/]+', '[0-9]+', '\/'), $key);
				// Does the RegEx match?
				$tmpReg = '/^'.$key.'$/i';
				$regRes = preg_match($tmpReg, $requestUri, $matches);
				$tmpMsg = sprintf("request_uri: %s, reg: %s, res: %s, matches: %s", $requestUri, $tmpReg, $regRes, json_encode($matches));
				$curLogger->debug($tmpMsg);
				if ($regRes) {
					$countMatches = count($matches);
					if ($countMatches == 1 && is_string($val)) {
						$requestUri = $val;
						break;
					} elseif ($countMatches == 2 && is_array($val)) {
						// format validation
						if (!(isset($val['uri']) && isset($val['query']) && !empty($val['uri']) && !empty($val['query']))) {
							throw new \Koala\Config\ConfigException("not valid router [{$key}] => " . json_encode($val) . " query and uri are both need", ErrorCode::INVALID_PARAM);
							continue;
						}
						$tmpCurArg = $matches[1];
						$_GET[$val['query']] = $tmpCurArg;
						$requestUri = $val['uri'];
						break;
					}
				}
			}
		}

		// set request uri
		$this->requestUri = $requestUri;
		$curLogger->debug("request uri: " . $this->requestUri, $_GET);
		$this->setRequest();
	}

	protected function setRequest() {
		$this->requestUri = trim($this->requestUri, '/');
		if (empty($this->requestUri)) {
			return;
		}
		$segments = explode('/', $this->requestUri);
		if (empty($segments)) {
			return;
		}
		$count = count($segments);
		if ($count == 1) {
			$this->module = '';
			$this->controller = $segments[0];
		} elseif ($count == 2) {
			$this->module = '';
			$this->controller = $segments[0];
			$this->action = $segments[1];
		} elseif ($count == 3) {
			$this->module = $segments[0];
			$this->controller = $segments[1];
			$this->action = $segments[2];
		} else {
			throw new RequestException("uri is not valid", ErrorCode::INVALID_PARAM);
		}
	}
}