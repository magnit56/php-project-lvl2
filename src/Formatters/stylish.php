<?php

namespace App\Formatters;

function renderStylish($ast)
{
    $iter = function ($ast, $depth) use (&$iter) {
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
    return $iter($ast, 1) . "\n";
}

function stringify($part, $depth)
{
    $type = gettype($part);
    switch ($type) {
        case "boolean":
            return $part ? "true" : "false";
        case "NULL":
            return "null";
        case "object":
            $baseIndent = "  ";
            $depthIndent = str_repeat("    ", $depth - 1);
            $indent = $baseIndent . $depthIndent;
            $bracketIndent = $depthIndent;

            $elements = [];
            foreach ($part as $key => $value) {
                $item = stringify($value, $depth + 1);
                $elements[] = "{$indent}  {$key}: {$item}";
            }
            $firstString = "{";
            $lastString = "{$bracketIndent}}";
            return implode("\n", [$firstString, ...$elements, $lastString]);
        default:
            return $part;
    }
}
