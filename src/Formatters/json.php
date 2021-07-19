<?php

namespace Differ\Formatters;

function renderJson(array $ast): string
{
    return strval(json_encode($ast, JSON_PRETTY_PRINT, JSON_THROW_ON_ERROR));
}
