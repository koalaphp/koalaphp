<?php
/**
 * Created by PhpStorm.
 * User: laiconglin
 * Date: 13/12/2017
 * Time: 19:48
 */

use Koala\Core\BaseController;

class ApiController extends BaseController
{
	public function testNumAction() {
		$id = isset($_GET["num"]) ? $_GET["num"] : "";
		$this->jsonOut(0, 'ok', ['id' => $id]);
		return false;
	}

	public function indexAction() {
		$this->jsonOut(0, 'ok', __METHOD__);
		return false;
	}
}