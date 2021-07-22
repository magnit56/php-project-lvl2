<?php

namespace Differ;

use Docopt;
use Exception;

use function Differ\Differ\genDiff;

function run(string $doc): void
{
    try {
        $args = Docopt::handle($doc, ['version' => 'Version 1.0']);
        print_r(genDiff($args['<firstFile>'], $args['<secondFile>'], $args['--format']));
    } catch (Exception $exception) {
        print_r($exception->getMessage());
    }
}
