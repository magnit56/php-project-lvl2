<?php

use PHPUnit\Framework\TestCase;
use App\Differ;

use function App\Differ\genDiff;

class DifferTest extends TestCase
{
    protected $extentions;
    protected $formats;

    protected function setUp(): void
    {
        $this->extentions = ['json'];
        $this->formats = ['stylish', 'plain'];
    }

    public function testDiffer()
    {
        foreach ($this->extentions as $extention) {
            $pathToBefore = __DIR__ . "/fixtures/inputData/{$extention}/before.{$extention}";
            $pathToAfter = __DIR__ . "/fixtures/inputData/{$extention}/after.{$extention}";

            $before = file_get_contents($pathToBefore);
            $after = file_get_contents($pathToAfter);

            foreach ($this->formats as $format) {
                $actual = genDiff($pathToBefore, $pathToAfter, $format);

                $pathToResult = __DIR__ . "/fixtures/outputData/{$format}/result";
                $expected = file_get_contents($pathToResult);

                $this->assertEquals($expected, $actual);
            }
        }
    }
}
