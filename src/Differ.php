<?php

namespace Differ\Differ;

use Exception;
use Differ\Parsers;
use Differ\Renderers;
use Functional\Functional;

use function Differ\Renderers\render;

const FORMATS = ["stylish", "plain", "json"];

function genDiff(string $path1, string $path2, string $format = "stylish"): string
{
    if (!in_array($format, FORMATS, true)) {
        throw new Exception("Данный формат не поддерживается программой");
    }
    if (!file_exists($path1)) {
        throw new Exception("Файл {$path1} не существует");
    }
    if (!file_exists($path2)) {
        throw new Exception("Файл {$path2} не существует");
    }

    $data1 = strval(file_get_contents($path1));
    $extension1 = pathinfo($path1, PATHINFO_EXTENSION);

    $data2 = strval(file_get_contents($path2));
    $extension2 = pathinfo($path2, PATHINFO_EXTENSION);

    $before = Parsers\parse($data1, $extension1);
    $after = Parsers\parse($data2, $extension2);

    $ast = getAst($before, $after);
    return render($ast, $format);
}

function getAst(object $before, object $after): array
{
    $beforeKeys = array_keys(get_object_vars($before));
    $afterKeys = array_keys(get_object_vars($after));

    $keys = array_unique([...$beforeKeys, ...$afterKeys]);
    $sortedKeys = \Functional\sort($keys, function ($left, $right, $collection) {
        if ($left === $right) {
            return 0;
        }
        return $left > $right ? 1 : -1;
    });

    $ast = array_map(function ($key) use ($before, $after) {
        if (isAdded($before, $after, $key)) {
            return [
                'name' => $key,
                'type' => 'added',
                'value' => $after->{$key}
            ];
        }
        if (isDeleted($before, $after, $key)) {
            return [
                'name' => $key,
                'type' => 'deleted',
                'value' => $before->{$key}
            ];
        }
        if (isNested($before, $after, $key)) {
            $children = getAst($before->{$key}, $after->{$key});
            return [
                'name' => $key,
                'type' => 'nested',
                'children' => $children
            ];
        }
        if (isChanged($before, $after, $key)) {
            return [
                'name' => $key,
                'type' => 'changed',
                'beforeValue' => $before->{$key},
                'afterValue' => $after->{$key}
            ];
        }
        if (isUnchanged($before, $after, $key)) {
            return [
                'name' => $key,
                'type' => 'unchanged',
                'value' => $before->{$key}
            ];
        }
    }, $sortedKeys);
    return $ast;
}

function isNested(object $elem1, object $elem2, string $key): bool
{
    $keyStatus = property_exists($elem1, $key) && property_exists($elem2, $key);
    return $keyStatus ? (is_object($elem1->{$key}) && is_object($elem2->{$key})) : false;
}

function isAdded(object $elem1, object $elem2, string $key): bool
{
    $keyStatus = !property_exists($elem1, $key) && property_exists($elem2, $key);
    return $keyStatus;
}

function isDeleted(object $elem1, object $elem2, string $key): bool
{
    $keyStatus = property_exists($elem1, $key) && !property_exists($elem2, $key);
    return $keyStatus;
}

function isChanged(object $elem1, object $elem2, string $key): bool
{
    $keyStatus = property_exists($elem1, $key) && property_exists($elem2, $key);
    return $keyStatus ? !equal($elem1->{$key}, $elem2->{$key}) : false;
}

function isUnchanged(object $elem1, object $elem2, string $key): bool
{
    $keyStatus = property_exists($elem1, $key) && property_exists($elem2, $key);
    return $keyStatus ? equal($elem1->{$key}, $elem2->{$key}) : false;
}

function equal(mixed $elem1, mixed $elem2): bool
{
    if (is_array($elem1) && is_array($elem2)) {
        return array_diff_assoc($elem1, $elem2) === array();
    }
    return $elem1 === $elem2;
}
