<?php

namespace Differ\Formatters;

use Ds\Map;

function renderStylish(array $ast): string
{
    $iter = function (array $ast, int $depth) use (&$iter): string {
        $baseIndent = "  ";
        $depthIndent = str_repeat("    ", $depth - 1);
        $indent = $baseIndent . $depthIndent;
        $bracketIndent = $depthIndent;

        $parts = array_map(function ($part) use ($iter, $depth, $indent) {
            switch ($part['type']) {
                case 'added':
                    $value = stringify($part['value'], $depth + 1);
                    return $indent . "+ {$part['name']}: {$value}";
                case 'deleted':
                    $value = stringify($part['value'], $depth + 1);
                    return $indent . "- {$part['name']}: {$value}";
                case 'unchanged':
                    $value = stringify($part['value'], $depth + 1);
                    return $indent . "  {$part['name']}: {$value}";
                case 'changed':
                    $beforeValue = stringify($part['beforeValue'], $depth + 1);
                    $afterValue = stringify($part['afterValue'], $depth + 1);
                    $before = $indent . "- {$part['name']}: {$beforeValue}";
                    $after = $indent . "+ {$part['name']}: {$afterValue}";
                    return implode("\n", [$before, $after]);
                case 'nested':
                    $children = $iter($part['children'], $depth + 1);
                    return $indent . "  {$part['name']}: " . $children;
            }
        }, $ast);
        return implode("\n", ["{", ...$parts, "{$bracketIndent}}"]);
    };
    return $iter($ast, 1);
}

function stringify(mixed $part, int $depth): string
{
    $type = gettype($part);
    switch ($type) {
        case "boolean":
            return boolval($part) ? "true" : "false";
        case "NULL":
            return "null";
        case "object":
            $baseIndent = "  ";
            $depthIndent = str_repeat("    ", $depth - 1);
            $indent = $baseIndent . $depthIndent;
            $bracketIndent = $depthIndent;

            $map = new Map(get_object_vars($part));
            $elements = $map->map(function (mixed $key, mixed $value) use ($depth, $indent): string {
                $item = stringify($value, $depth + 1);
                return "{$indent}  {$key}: {$item}";
            })->values()->toArray();

            $firstString = "{";
            $lastString = "{$bracketIndent}}";
            return implode("\n", [$firstString, ...$elements, $lastString]);
        default:
            return strval($part);
    }
}
