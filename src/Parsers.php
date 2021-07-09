<?php

namespace App\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse($data, $type)
{
	$mapping = [
		'json' => json_decode($data),
		'yaml' => Yaml::parse($data)
	];
	return $mapping[$type]($data);
}
