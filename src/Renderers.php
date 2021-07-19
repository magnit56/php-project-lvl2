<?php

namespace Differ\Renderers;

use Exception;

use function Differ\Formatters\renderStylish;
use function Differ\Formatters\renderPlain;
use function Differ\Formatters\renderJson;

/**
 * @throws Exception
 */
function render(array $ast, string $format): string
{
    return match ($format) {
        'stylish' => renderStylish($ast),
        'plain' => renderPlain($ast),
        'json' => renderJson($ast),
        default => throw new Exception('Такой формат не поддерживается программой'),
    };
}
