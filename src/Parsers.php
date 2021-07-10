<?php

namespace App\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse($data, $ext)
{
    $mapping = [
        'json' => json_decode($data),
        'yaml' => Yaml::parse($data)
    ];
    return $mapping[$ext];
}
