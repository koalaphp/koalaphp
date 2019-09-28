<?php
/**
 * Created by PhpStorm.
 * User: laiconglin
 * Date: 2019/9/21
 * Time: 21:04
 */

use Koala\Core\Request;

use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
	public function testApp() {
		echo php_sapi_name() . PHP_EOL;
		$this->assertEquals("cli", php_sapi_name());
		// load bootstrap class
		$app = new \Koala\Core\Application();
		// 模拟请求的路径
		Request::getInstance()->setRequestUri("/user/home/show");
		$app->bootstrap();
		$app->run();
	}

	/**
	 * @expectedException \Koala\Core\Exception\UrlNotFoundException
	 */
	public function testErrorMethod() {
		// load bootstrap class
		$app = new \Koala\Core\Application();
		// 模拟请求的路径
		Request::getInstance()->setRequestUri("/api/error");
		$app->bootstrap();
		$app->run();
	}

	/**
	 * @expectedException \Koala\Core\Exception\UrlNotFoundException
	 */
	public function testErrorController() {
		// load bootstrap class
		$app = new \Koala\Core\Application();
		// 模拟请求的路径
		Request::getInstance()->setRequestUri("/api1/error");
		$app->bootstrap();
		$app->run();
	}

	public function testIndex() {
		echo php_sapi_name() . PHP_EOL;
		$this->assertEquals("cli", php_sapi_name());
		// load bootstrap class
		$app = new \Koala\Core\Application();
		// 模拟请求的路径
		Request::getInstance()->setRequestUri("/");
		$app->bootstrap();
		$app->run();
	}

	public function testIndexPhp() {
		echo php_sapi_name() . PHP_EOL;
		$this->assertEquals("cli", php_sapi_name());
		// load bootstrap class
		$app = new \Koala\Core\Application();
		// 模拟请求的路径
		Request::getInstance()->setRequestUri("/index.php/me");
		$app->bootstrap();
		$app->run();
	}

	public function testApiIndex() {
		echo php_sapi_name() . PHP_EOL;
		$this->assertEquals("cli", php_sapi_name());
		// load bootstrap class
		$app = new \Koala\Core\Application();
		// 模拟请求的路径
		Request::getInstance()->setRequestUri("/api/");
		$app->bootstrap();
		$app->run();
	}

	public function testWhoami() {
		echo php_sapi_name() . PHP_EOL;
		$this->assertEquals("cli", php_sapi_name());
		// load bootstrap class
		$app = new \Koala\Core\Application();
		// 模拟请求的路径
		Request::getInstance()->setRequestUri("/me");
		$app->bootstrap();
		$app->run();
	}

	public function testNum() {
		echo php_sapi_name() . PHP_EOL;
		$this->assertEquals("cli", php_sapi_name());
		// load bootstrap class
		$app = new \Koala\Core\Application();
		// 模拟请求的路径
		Request::getInstance()->setRequestUri("/12345?debug=1");
		$app->bootstrap();
		$app->run();
	}
}
