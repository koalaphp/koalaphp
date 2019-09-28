<?php
/**
 * Created by PhpStorm.
 * User: laiconglin
 * Date: 13/12/2017
 * Time: 21:45
 */

use Koala\Core\BaseController;

class IndexController extends BaseController
{
	public function indexAction() {
		$data = [
			'hello' => \Koala\Core\Helpers\Util::getClientIp(),
		];
		$this->render('index/index', $data);
		return false;
	}

	public function whoamiAction() {
		$data = [
			'hello' => __METHOD__,
		];
		$this->render('index/index', $data);
		return false;
	}
}
