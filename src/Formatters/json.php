<?php

namespace Differ\Formatters;

function renderJson(array $ast): mixed
{
    return json_encode($ast, JSON_PRETTY_PRINT);
}
