<?php

namespace Differ\Formatters;

function renderJson(array $ast): string|false
{
    return json_encode($ast, JSON_PRETTY_PRINT);
}
