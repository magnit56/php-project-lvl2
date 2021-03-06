<?php

namespace App;

use Docopt;
use Exception;

use function App\Differ\genDiff;

function run($doc)
{
    $args = Docopt::handle($doc, ['version' => 'Version 1.0']);

    try {
        $difference = genDiff($args['<firstFile>'], $args['<secondFile>'], $args['--format']);
        print_r($difference);
    } catch (Exception $exception) {
        print_r($exception->getMessage());
        exit(1);
    }
}
