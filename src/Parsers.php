<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse(string $data, string $ext): object
{
    $mapping = [
        'json' => json_decode($data),
        'yaml' => Yaml::parse($data, YAML::PARSE_OBJECT_FOR_MAP)
    ];
    return $mapping[$ext];
}
