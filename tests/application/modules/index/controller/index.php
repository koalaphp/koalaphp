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
		$this->jsonOut(0, 'ok', __METHOD__);
		return false;
	}
}
