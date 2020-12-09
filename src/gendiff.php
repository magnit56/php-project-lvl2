<?php

namespace GenDiff\GenDiff;

function printHelp()
{
    $doc = <<<DOC
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)

Options:
  -h --help                     Show this screen
  -v --version                  Show version
DOC;

    $params = array(
        'argv' => array_slice($_SERVER['argv'], 1),
        'help' => true,
        'version' => null,
        'optionsFirst' => false,
    );
    $args = \Docopt::handle($doc, $params);
    foreach ($args as $k => $v)
        print_r($k . ': ' . json_encode($v) . PHP_EOL);
}
