<?php
/**
 * Created by PhpStorm.
 * User: laiconglin
 * Date: 26/11/2017
 * Time: 10:35
 */
namespace Koala\Core;

use Koala\Config\ConfigPool;
use Koala\Core\Exception\ErrorCode;
use Koala\Core\Exception\UrlNotFoundException;
use Koala\Log\Logger;

class Application
{
	use SingletonTrait;

	public $curRouter = null;

	public function __construct() {
	}

	protected $appConfig = [];

	public function initConfig() {
		$this->appConfig = ConfigPool::getConfig('app');
	}

	public function initLogger() {
		$logConfig = ConfigPool::getConfig('log');
		\Koala\Log\Logger::initLogConfig($logConfig);
	}

	public function bootstrap() {
		// 初始化app配置文件
		$this->initConfig();
		// 初始化Logger日志文件组件
		$this->initLogger();

		// 初始化全局的异常处理函数
		register_shutdown_function('\Koala\Core\Application::check_for_fatal' );
		set_error_handler('\Koala\Core\Application::log_error');
	}

	public function run() {
		$request = \Koala\Core\Request::getInstance();
		$curRequestUri = $request->getRequestUri();
		$router = new \Koala\Core\Router($curRequestUri);
		$this->curRouter = $router;
		$this->execute($router);
	}

	/**
	 * 执行具体的控制器的动作
	 *
	 * @param \Koala\Core\Router $router
	 * @throws UrlNotFoundException
	 */
	public function execute(\Koala\Core\Router $router) {
		if (empty($router)) {
			throw new UrlNotFoundException(__METHOD__ . " router can't be null ", ErrorCode::URL_NOT_FOUND);
		}
		$modulePath = APP_PATH . DS . "modules" . DS;
		if (empty($router->module)) {
			$willLoadFile = $modulePath . "index" . DS . "controller" . DS . $router->controller . ".php";
		} else {
			$willLoadFile = $modulePath . $router->module . DS . "controller" . DS . $router->controller . ".php";
		}
		$showPath = str_replace(APP_ROOT, '', $willLoadFile);
		if (!file_exists($willLoadFile)) {
			throw new UrlNotFoundException(__METHOD__ . " file [{$showPath}] not exists", ErrorCode::URL_NOT_FOUND);
		}
		if (!is_readable($willLoadFile)) {
			throw new UrlNotFoundException(__METHOD__ . " file [{$showPath}] is not readable", ErrorCode::URL_NOT_FOUND);
		}
		require_once $willLoadFile;
		$willLoadClass = ucfirst($router->controller) . "Controller";
		if (!class_exists($willLoadClass, true)) {
			throw new UrlNotFoundException(__METHOD__ .  " class [{$willLoadClass}] not found: ", ErrorCode::URL_NOT_FOUND);
		}
		$curControllerObj = new $willLoadClass($router);
		$curMethodName = $router->action . 'Action';
		if (!method_exists($curControllerObj, $curMethodName)) {
			throw new UrlNotFoundException("method [{$curMethodName}] not found in class " . $willLoadClass, ErrorCode::URL_NOT_FOUND);
		}
		$curMethodVariable = array($curControllerObj, $curMethodName);
		if (!is_callable($curMethodVariable, true, $callableName)) {
			throw new UrlNotFoundException("method [{$curMethodName}] are not callable in class " . $willLoadClass, ErrorCode::URL_NOT_FOUND);
		}
		$reflectMethod = new \ReflectionMethod($willLoadClass, $curMethodName);
		$numberParams = count($reflectMethod->getParameters());
		if ($numberParams) {
			throw new UrlNotFoundException("class {$willLoadClass}, method [{$curMethodName}]'s parameter must be void.", ErrorCode::URL_NOT_FOUND);
		}
		$curControllerObj->$curMethodName();
	}

	/**
	 * 记录错误进日志
	 *
	 * @param $num
	 * @param $str
	 * @param $file
	 * @param $line
	 * @param null $context
	 */
	public static function log_error($num, $str, $file, $line, $context = null )
	{
		self::log_exception(new \ErrorException( $str, 500, $num, $file, $line ) );
	}

	public static function log_exception(\Exception $e ) {
		$message = "Type: " . get_class( $e ) . "; Message: {$e->getMessage()}; File: {$e->getFile()}; Line: {$e->getLine()};";
		$fatalErrorLogger = Logger::getLogger('fatal-error');
		$fatalErrorLogger->error($message);
	}

	public static function check_for_fatal()
	{
		$error = error_get_last();
		if ( $error["type"] == E_ERROR ) {
			self::log_error( $error["type"], $error["message"], $error["file"], $error["line"] );
		}
	}
}

