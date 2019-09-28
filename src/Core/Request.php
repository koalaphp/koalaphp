<?php
/**
 * Created by PhpStorm.
 * User: laiconglin
 * Date: 26/11/2017
 * Time: 21:09
 */

namespace Koala\Core;
use Koala\Core\Helpers\Util;
use Koala\Core\Exception\RequestException;

class Request
{
	use SingletonTrait;

	/**
	 * Allowed URL Characters
	 * DO NOT CHANGE THIS UNLESS YOU FULLY UNDERSTAND THE REPERCUSSIONS!!
	 * @var string
	 */
	protected $permitUriChars = 'a-z0-9_\-\.';

	protected $router;

	/**
	 * 当前访问的request uri，例如：/user/api/hello
	 *
	 * @var string
	 */
	protected $requestUri = '/';

	public function __construct() {
		$this->requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/index/index/index';
		$this->processUri();
	}

	/**
	 * provide for php unit test
	 * @param string $uri
	 */
	public function setRequestUri($uri = '/') {
		$this->requestUri = $uri;
		$this->processUri();
	}

	public function getRequestUri() {
		return $this->requestUri;
	}

	protected function processUri() {
		$uri = $this->requestUri;

		// remove string after "?"
		$questionPos = strpos($uri, '?');
		if ($questionPos !== false) {
			$uri = substr($uri, 0, $questionPos);
		}

		if ($uri === '/' || $uri === '') {
			$this->requestUri = '/';
			return ;
		}

		// remove '/index.php'
		$uri = trim($uri, '/');
		if (strpos($uri, 'index.php') !== false && strpos($uri, 'index.php') === 0) {
			$uri = str_replace('index.php', '', $uri);
		}

		$uri = Util::removeInvisibleCharacters($uri);
		// remove relative directory ".."
		$uri = $this->removeRelativeDirectory($uri);

		$segments = [];

		foreach (explode('/', $uri) as $tmpSeg) {
			// only allow specific chars
			if ( ! empty($tmpSeg) && ! empty($this->permitUriChars) && ! preg_match('/^['.$this->permitUriChars.']+$/i'.(UTF8_ENABLED ? 'u' : ''), $tmpSeg)) {
				throw new RequestException('The URI you submitted has disallowed characters. [' . $uri . ']', 400);
			}
			$segments[] = $tmpSeg;
		}

		$this->requestUri = "/" . implode("/", $segments);
	}

	/**
	 * Remove relative directory (../) and multi slashes (///)
	 *
	 * Do some final cleaning of the URI and return it, currently only used in self::_parse_request_uri()
	 *
	 * @param	string	$uri
	 * @return	string
	 */
	protected function removeRelativeDirectory($uri) {
		$uris = array();
		$tok = strtok($uri, '/');
		while ($tok !== FALSE) {
			if (( ! empty($tok) OR $tok === '0') && $tok !== '..') {
				$uris[] = $tok;
			}
			$tok = strtok('/');
		}

		return implode('/', $uris);
	}
}