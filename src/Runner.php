<?php

namespace Differ;

use Docopt;
use Exception;

use function Differ\Differ\genDiff;

function run($doc)
{
    $args = Docopt::handle($doc, ['version' => 'Version 1.0']);

    try {
        $difference = "'" . genDiff($args['<firstFile>'], $args['<secondFile>'], $args['--format']) . "'" . "\n";
        print_r($difference);
    } catch (Exception $exception) {
        print_r($exception->getMessage());
        exit(1);
    }
}
