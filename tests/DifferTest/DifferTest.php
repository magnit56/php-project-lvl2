<?php

namespace DifferTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    protected $extentions;
    protected $formats;

    protected function setUp(): void
    {
        $this->extentions = ['json', 'yaml'];
        $this->formats = ['stylish', 'plain', 'json'];
    }

    public function testDiffer()
    {
        foreach ($this->extentions as $extention) {
            $pathToBefore = __DIR__ . "/../fixtures/inputData/{$extention}/before.{$extention}";
            $pathToAfter = __DIR__ . "/../fixtures/inputData/{$extention}/after.{$extention}";

            $before = file_get_contents($pathToBefore);
            $after = file_get_contents($pathToAfter);

            foreach ($this->formats as $format) {
                $actual = genDiff($pathToBefore, $pathToAfter, $format);

                $pathToResult = __DIR__ . "/../fixtures/outputData/{$format}/result";
                $expected = file_get_contents($pathToResult);

                $this->assertEquals($expected, $actual);
            }
        }
    }
}
