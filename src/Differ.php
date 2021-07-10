<?php

namespace App\Differ;

use Exception;
use App\Parsers;
use App\Renderers;

use function App\Renderers\render;
use function Funct\Collection\union;

const FORMATS = ["stylish", "plain", "json"];

function genDiff($path1, $path2, $format = "stylish")
{
    if (!in_array($format, FORMATS)) {
        throw new Exception("Данный формат не поддерживается программой");
    }
    if (!file_exists($path1)) {
        throw new Exception("Файл {$path1} не существует");
    }
    if (!file_exists($path2)) {
        throw new Exception("Файл {$path2} не существует");
    }

    $data1 = file_get_contents($path1);
    $extension1 = pathinfo($path1, PATHINFO_EXTENSION);

    $data2 = file_get_contents($path2);
    $extension2 = pathinfo($path2, PATHINFO_EXTENSION);

    $before = Parsers\parse($data1, $extension1);
    $after = Parsers\parse($data2, $extension2);

    $ast = getAst($before, $after);
    return render($ast, $format);
}

function getAst($before, $after)
{
    $beforeKeys = array_keys(get_object_vars($before));
    $afterKeys = array_keys(get_object_vars($after));

    $keys = union($beforeKeys, $afterKeys);
    sort($keys);

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
    }, $keys);
    return $ast;
}

function isNested($elem1, $elem2, $key)
{
    $keyStatus = property_exists($elem1, $key) && property_exists($elem2, $key);
    return $keyStatus ? (is_object($elem1->{$key}) && is_object($elem2->{$key})) : false;
}

function isAdded($elem1, $elem2, $key)
{
    $keyStatus = !property_exists($elem1, $key) && property_exists($elem2, $key);
    return $keyStatus;
}

function isDeleted($elem1, $elem2, $key)
{
    $keyStatus = property_exists($elem1, $key) && !property_exists($elem2, $key);
    return $keyStatus;
}

function isChanged($elem1, $elem2, $key)
{
    $keyStatus = property_exists($elem1, $key) && property_exists($elem2, $key);
    return $keyStatus ? !equal($elem1->{$key}, $elem2->{$key}) : false;
}

function isUnchanged($elem1, $elem2, $key)
{
    $keyStatus = property_exists($elem1, $key) && property_exists($elem2, $key);
    return $keyStatus ? equal($elem1->{$key}, $elem2->{$key}) : false;
}

function equal($elem1, $elem2)
{
    if (is_array($elem1) && is_array($elem2)) {
        return empty(array_diff_assoc($elem1, $elem2));
    }
    return $elem1 === $elem2;
}
