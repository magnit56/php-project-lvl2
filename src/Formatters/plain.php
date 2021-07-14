<?php

namespace App\Formatters;

function renderPlain($ast): string
{
    $iter = function ($ast, $parents) use (&$iter) {
        $parts = array_map(
            function ($part) use ($iter, $parents) {
                switch ($part['type']) {
                    case 'added':
                        $propertyFullName = getPropertyFullName($part['name'], $parents);
                        $value = getStringValue($part['value']);
                        return "Property '{$propertyFullName}' was added with value: {$value}";
                    case 'deleted':
                        $propertyFullName = getPropertyFullName($part['name'], $parents);
                        return "Property '{$propertyFullName}' was removed";
                    case 'unchanged':
                        break;
                    case 'changed':
                        $propertyFullName = getPropertyFullName($part['name'], $parents);
                        $beforeValue = getStringValue($part['beforeValue']);
                        $afterValue = getStringValue($part['afterValue']);
                        return "Property '{$propertyFullName}' was updated. From {$beforeValue} to {$afterValue}";
                    case 'nested':
                        $propertyName = $part['name'];
                        $childrenParents = [...$parents, $propertyName];
                        return $iter($part['children'], $childrenParents);
                }
            },
            $ast
        );
        return implode("\n", array_filter($parts));
    };
    return $iter($ast, []);
}

function getStringValue($value)
{
    $type = gettype($value);
    switch ($type) {
        case "boolean":
            return $value ? "true" : "false";
        case "NULL":
            return "null";
        case "object":
            return "[complex value]";
        case "string":
            return "'{$value}'";
        default:
            return $value;
    }
}

function getPropertyFullName($propertyName, $parents): string
{
    return implode(".", [...$parents, $propertyName]);
}
