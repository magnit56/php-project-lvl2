<?php

namespace App\Formatters;

function renderJson($ast): string
{
    return json_encode($ast, JSON_PRETTY_PRINT);
}