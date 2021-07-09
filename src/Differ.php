<?php

namespace App\GenDiff;

use Exception;

use App\Parsers;

const FORMATS = ["stylish", "plain"];

function genDiff($path1, $path2, $format = "stylish"): array
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
    print_r($extension1);
    print_r($extension2);

    //print_r($data1ToArray);
    //print_r($data2ToArray);
    //return [$data1ToArray, $data2ToArray];
    return [];
}

function diffFinder($elem1, $elem2)
{

}