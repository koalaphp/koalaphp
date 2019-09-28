<?php
/**
 * Created by PhpStorm.
 * User: laiconglin
 * Date: 13/12/2017
 * Time: 19:49
 */

namespace Koala\Core;

use Koala\Core\Exception\ErrorCode;
use Koala\Core\Exception\RequestException;
use Koala\Core\Exception\TemplateRenderException;
use Koala\Core\Helpers\Util;
use Koala\Log\Logger;

class BaseController
{
	/**
	 * 是否已经输出过了json
	 * @var bool
	 */
	protected $isJsonOuted = false;
	/**
	 * 是否已经渲染了模板
	 * @var bool
	 */
	protected $isRenderPaged = false;

	protected $curObLevel;

	protected $curRouter = null;

	/**
	 * 通用模板的路径
	 * @var string
	 */
	protected $commonLayoutPath = APP_PATH . DS . "view"  . DS . "layout.php";

	public function __construct(\Koala\Core\Router $router)
	{
		$this->curObLevel = ob_get_level();
		$this->curRouter = $router;
		// init
		$this->init();
	}

	/**
	 * 初始化函数
	 */
	protected function init() {

	}
	/**
	 * 渲染模板会自动带上公共顶部，左侧导航栏等。
	 *
	 * @param string $tpl 模板名称 (从当前请求的module的view目录下开始)
	 * @param array $data 赋给模板的数据
	 * @return bool | string
	 *
	 * @throws RequestException
	 */
	protected function render($tpl, $data = []) {
		// 生成内容
		$relativePathTpl = $tpl . ".php";
		$fullPath = APP_PATH . DS . "modules" . DS . (empty($this->curRouter->module) ? 'index' : $this->curRouter->module) . DS . "view" . DS . $relativePathTpl;
		$content = $this->display($fullPath, $data, true);
		// 拼接最终的内容，把自己的模板的添加变量中去
		$data['mainContent'] = $content;
		return $this->display($this->commonLayoutPath, $data);
	}

	/**
	 * 渲染某个模板
	 * @param string $fullTplPath  模板的完成的路径
	 * @param array $data
	 * @param bool $isReturn
	 * @return bool|string
	 * @throws TemplateRenderException
	 */
	protected function display($fullTplPath, $data = [], $isReturn = false) {
		if (empty($fullTplPath)) {
			throw new TemplateRenderException("{$fullTplPath} is empty.", ErrorCode::TPL_ERROR);
		}

		if ($this->isJsonOuted) {
			throw new TemplateRenderException("already output json before, can't render page.", ErrorCode::TPL_ERROR);
		}

		$showPath = str_replace(APP_ROOT, '', $fullTplPath);
		if (!file_exists($fullTplPath)) {
			throw new TemplateRenderException("{$showPath} is not found.", ErrorCode::TPL_ERROR);
		}

		if ( !is_readable($fullTplPath)) {
			throw new TemplateRenderException("{$showPath} is not readable.", ErrorCode::TPL_ERROR);
		}

		if (is_dir($fullTplPath)) {
			throw new TemplateRenderException("{$showPath} is a directory.", ErrorCode::TPL_ERROR);
		}

		if (is_array($data) && !empty($data)) {
			extract($data);
		}

		// @todo 增加输出缓冲
		// 简化输出的内容，不支持嵌套输出, 可以将公共部分放到变量中，再输出渲染
		/*
		 * Buffer the output
		 *
		 * We buffer the output for two reasons:
		 * 1. Speed. You get a significant speed boost.
		 * 2. So that the final rendered template can be post-processed by
		 *	the output class. Why do we need post processing? For one thing,
		 *	in order to show the elapsed page load time. Unless we can
		 *	intercept the content right before it's sent to the browser and
		 *	then stop the timer it won't be accurate.
		 */
		ob_start();

		$msg = sprintf("%s, ob_get_level:%s, curLevel: %s", $fullTplPath, ob_get_level(), $this->curObLevel);
		Logger::getLogger("app")->debug($msg);

		include($fullTplPath);
		if ($isReturn) {
			$buffer = ob_get_contents();
			@ob_end_clean();
			return $buffer;
		}
		// @todo 增加输出缓冲
		/*
		 * Flush the buffer... or buff the flusher?
		 *
		 * In order to permit views to be nested within
		 * other views, we need to flush the content back out whenever
		 * we are beyond the first level of output buffering so that
		 * it can be seen and included properly by the first included
		 * template and any subsequent ones. Oy!
		 */
		$outputBuffer = null;
		if (ob_get_level() > $this->curObLevel + 1) {

			$msg = sprintf("%s, ob_get_level:%s, curLevel: %s, ob_end_flush start", $fullTplPath, ob_get_level(), $this->curObLevel);
			Logger::getLogger("app")->debug($msg);

			ob_end_flush();

			$msg = sprintf("%s, ob_get_level:%s, curLevel: %s, ob_end_flush end", $fullTplPath, ob_get_level(), $this->curObLevel);
			Logger::getLogger("app")->debug($msg);

		} else {
			$msg = sprintf("%s, ob_get_level:%s, curLevel: %s, ob_end_clean start", $fullTplPath, ob_get_level(), $this->curObLevel);
			Logger::getLogger("app")->debug($msg);

			$outputBuffer = ob_get_contents();
			@ob_end_clean();

			$msg = sprintf("%s, ob_get_level:%s, curLevel: %s, ob_end_clean end", $fullTplPath, ob_get_level(), $this->curObLevel);
			Logger::getLogger("app")->debug($msg);
		}

		if ($outputBuffer !== null) {
			echo $outputBuffer;
			// 设置已经渲染过了页面
			$this->isRenderPaged = true;
		}

		return true;
	}

	/**
	 * 输出json格式的内容
	 * @param int $code
	 * @param string $msg
	 * @param array $data
	 * @throws RequestException
	 *
	 */
	protected function jsonOut($code = 0, $msg = 'ok', $data = []) {
		if ($this->isRenderPaged) {
			throw new RequestException("already render page before, can't output json content.", ErrorCode::INVALID_PARAM);
		}
		if (Util::isCli() === false) {
			header('Content-Type: application/json; charset=utf-8');
		}
		echo json_encode([
			'code' => intval($code),
			'msg' => strval($msg),
			'data' => $data,
		], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		$this->isJsonOuted = true;
	}
}