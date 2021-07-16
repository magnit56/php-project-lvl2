<?php

namespace Differ\Renderers;

use function Differ\Formatters\renderStylish;
use function Differ\Formatters\renderPlain;
use function Differ\Formatters\renderJson;

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
