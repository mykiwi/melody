<?php

namespace SensioLabs\Melody\Tests\Handler;

use PHPUnit\Framework\TestCase;
use SensioLabs\Melody\Handler\StreamHandler;

class StreamHandlerTest extends TestCase
{
    private $handler;

    protected function setUp(): void
    {
        $this->handler = new StreamHandler();
    }

    protected function tearDown(): void
    {
        $this->handler = null;
    }

    public function provideSupports()
    {
        return array(
            array(__DIR__.'/../Fixtures/hello-world.php', true),
            array(__DIR__.'/../Fixtures/Path/To/InvalidFile.php', false),
            array('php://stdin', true),
            array('https://gist.githubusercontent.com/lyrixx/565752f13499a3fa17d9/raw/f561159b09a2014dd0d75727582c2cf8ee36e30e/melody.php', true),
        );
    }

    /**
     * @dataProvider provideSupports
     */
    public function testSupports($path, $expected)
    {
        $this->assertEquals($expected, $this->handler->supports($path));
    }

    public function testCreateResource()
    {
        $filename = __DIR__.'/../Fixtures/hello-world.php';

        $resource = $this->handler->createResource($filename);

        $this->assertInstanceOf('SensioLabs\Melody\Resource\Resource', $resource);
        $this->assertSame(file_get_contents($filename), $resource->getContent());
    }
}
