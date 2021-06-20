<?php

namespace SensioLabs\Melody\Tests\Handler\Github;

use PHPUnit\Framework\TestCase;
use SensioLabs\Melody\Handler\Github\Gist;

class GistTest extends TestCase
{
    public function provideInvalidUrl()
    {
        return array(
            array(''),
            array('http://foo'),
            array('https://foo/bar'),
            array('http://gist.foo.bar/bar/123'),
        );
    }

    /**
     * @dataProvider      provideInvalidUrl
     */
    public function testInvalidUrl($invalidUrl)
    {
        $this->expectException('InvalidArgumentException');

        $gist = new Gist($invalidUrl);
    }
}
