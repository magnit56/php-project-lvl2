<?php

namespace App\Renderers;

use function App\Formatters\renderStylish;
use function App\Formatters\renderPlain;
use function App\Formatters\renderJson;

function render($ast, $format)
{
    switch ($format) {
        case 'stylish':
            return renderStylish($ast);
        case 'plain':
            return renderPlain($ast);
        case 'json':
            return renderJson($ast);
    }
}
