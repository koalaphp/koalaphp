<?php
/**
 * Created by PhpStorm.
 * User: laiconglin
 * Date: 26/11/2017
 * Time: 23:41
 */

// too many routes will reduce performance
// Wildcards are actually aliases for regular expressions,
// with ":any" being translated to [^/]+ and ":num" to [0-9]+, respectively.

return [
	"/me" => "/index/whoami", // "/me" will be convert to "/user/home/whoami"
	"/(:num)" => [
		// "/(:num)" will be convert to "/api/testNum?id=:num", "uri" and "query" are both required
		"uri" => "/api/testNum",
		"query" => "name",
	],
	"/user/api/(:any)" => [
		// "/user/api/(:any)" will be convert to "/user/home/queryAny?id=:num", "uri" and "query" are both required
		"uri" => "/user/home/queryAny",
		"query" => "id",
	],
];
