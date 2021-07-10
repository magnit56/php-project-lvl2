<?php

namespace App\Renderers;

use function App\Formatters\renderStylish;

function render($ast, $format)
{
    switch ($format) {
        case 'stylish':
            return renderStylish($ast);
        case 'pretty':
            return renderPretty($ast);
        case 'json':
            return renderJson($ast);
    }
}
