<?php


namespace App\Tests\Service;


use App\Service\DinosaurLengthDeterminator;
use PHPUnit\Framework\TestCase;

class DinosaurLengthDeterminatorTest extends TestCase
{
    /**
     * @dataProvider getDinosaurSpecTests
     */
    public function testItReturnsCorrectLengthRange($spec, $minExpectedSize, $maxExpectedSize)
    {
        $determinate = new DinosaurLengthDeterminator();
        $length = $determinate->getLengthFromSpecification($spec);

        $this->assertGreaterThanOrEqual($minExpectedSize, $length);
        $this->assertLessThanOrEqual($maxExpectedSize, $length);
    }

    public function getDinosaurSpecTests()
    {
        return [
            // specification, min length, max length
            ['small dino', 1, 19],
            ['large dino', 20, 29],
            ['huge dinosaur',29, 100 ],
            ['huge dino', 29, 100],
            ['huge', 29, 100],
            ['OMG', 29, 100],
            ['😱', 29, 100],
        ];
    }
}