<?php

namespace Differ\Formatters;

function renderPlain(array $ast): string
{
    $iter = function (array $ast, array $parents) use (&$iter): string {
        $parts = array_map(
            function (array $part) use ($iter, $parents): string {
                switch ($part['type']) {
                    case 'added':
                        $propertyFullName = getPropertyFullName($part['name'], $parents);
                        $value = getStringValue($part['value']);
                        return "Property '{$propertyFullName}' was added with value: {$value}";
                    case 'deleted':
                        $propertyFullName = getPropertyFullName($part['name'], $parents);
                        return "Property '{$propertyFullName}' was removed";
                    case 'changed':
                        $propertyFullName = getPropertyFullName($part['name'], $parents);
                        $beforeValue = getStringValue($part['beforeValue']);
                        $afterValue = getStringValue($part['afterValue']);
                        return "Property '{$propertyFullName}' was updated. From {$beforeValue} to {$afterValue}";
                    case 'nested':
                        $propertyName = $part['name'];
                        $childrenParents = [...$parents, $propertyName];
                        return $iter($part['children'], $childrenParents);
                    default:
                        return "";
                }
            },
            $ast
        );
        return implode("\n", array_filter($parts));
    };
    return $iter($ast, []);
}

function getStringValue(mixed $value): string
{
    $type = gettype($value);
    return match ($type) {
        "boolean" => boolval($value) ? "true" : "false",
        "NULL" => "null",
        "object" => "[complex value]",
        "string" => "'{$value}'",
        "integer", "double" => "{$value}",
        "array" => "[array]",
        default => strval($value),
    };
}

function getPropertyFullName(string $propertyName, array $parents): string
{
    return implode(".", [...$parents, $propertyName]);
}
