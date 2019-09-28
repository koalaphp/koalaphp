<?php
/**
 * Created by PhpStorm.
 * User: laiconglin
 * Date: 26/11/2017
 * Time: 20:26
 */
namespace Koala\Core\Helpers;

class Util
{
	/**
	 * 强制转化为int整型数组，并且过滤掉为0的值
	 * （对于获取ID列表很有用）
	 *
	 * @param array $arr
	 * @return array
	 */
	public static function forceIntvalFilterUnique($arr = [])
	{
		return array_values(array_unique(array_filter(array_map('intval', $arr))));
	}

	public static function prettyJsonOut($data)
	{
		return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	}

	/**
	 * Remove Invisible Characters
	 *
	 * This prevents sandwiching null characters
	 * between ascii characters, like Java\0script.
	 *
	 * @param    string
	 * @param    bool
	 * @return    string
	 */
	public static function removeInvisibleCharacters($str, $url_encoded = TRUE)
	{
		$non_displayables = array();

		// every control character except newline (dec 10),
		// carriage return (dec 13) and horizontal tab (dec 09)
		if ($url_encoded) {
			$non_displayables[] = '/%0[0-8bcef]/';    // url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/';    // url encoded 16-31
		}

		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';    // 00-08, 11, 12, 14-31, 127

		do {
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		} while ($count);

		return $str;
	}

	public static function getCookie($name = '')
	{
		if (empty($name)) {
			return '';
		}
		if (isset($_COOKIE[$name]) && is_string($_COOKIE[$name])) {
			return $_COOKIE[$name];
		}
		return '';
	}

	public static function setCookie($name = '', $value = '', $expire = NOW_TIME + 3600, $domain = '', $isHttpOnly = true)
	{
		setcookie($name, $value, $expire, '/', $domain, false, $isHttpOnly);
	}

	public static function redirect($url = '/')
	{
		header("Location: " . $url);
		exit(0);
	}

	public static function printAndExit($data = [])
	{
		echo "<pre>";
		print_r($data);
		echo "</pre>";
		exit;
	}

	public static function getClientIp()
	{
		$ip = '';
		/**
		 *  nginx 转发:
		 *
		 *  proxy_set_header Host $host;
		 *  proxy_set_header X-Real-IP $remote_addr;
		 *  proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
		 */
		if (isset($_SERVER['HTTP_X_REAL_IP']) && !empty($_SERVER['HTTP_X_REAL_IP'])) {
			$ip = $_SERVER['HTTP_X_REAL_IP'];
		} elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		if(!filter_var($ip, FILTER_VALIDATE_IP, '')) {
			$ip = '127.0.0.1';
		}
		return $ip;
	}

	/**
	 * 是否是cli命令行
	 * @return bool
	 */
	public static function isCli() {
		return php_sapi_name() === "cli";
	}
}