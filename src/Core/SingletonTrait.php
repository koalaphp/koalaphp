<?php
/**
 * Created by PhpStorm.
 * User: laiconglin
 * Date: 01/01/2017
 * Time: 22:12
 */

namespace Koala\Core;

Trait SingletonTrait
{
	private static $singletonInstance = null;
	/**
	 * @return static
	 */
	public static function getInstance() {
		if (static::$singletonInstance == null) {
			static::$singletonInstance = new static();
		}
		return static::$singletonInstance;
	}
}