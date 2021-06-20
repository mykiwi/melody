<?php

namespace SensioLabs\Melody\Tests;

use PHPUnit\Framework\TestCase;
use SensioLabs\Melody\Configuration\RunConfiguration;
use SensioLabs\Melody\Configuration\UserConfiguration;
use SensioLabs\Melody\Melody;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * @group integration
 */
class IntegrationTest extends TestCase
{
    private $fs;

    protected function setUp(): void
    {
        $this->fs = new Filesystem();
        $this->cleanCache();
    }

    protected function tearDown(): void
    {
        $this->cleanCache();
        $this->fs = null;
    }

    public function testRunWithDefaultOption()
    {
        $output = $this->melodyRunFixture('hello-world.php');
        $this->assertStringContainsString('Loading composer repositories with package information', $output);
        $this->assertStringContainsString('Updating dependencies', $output);
        $this->assertStringContainsString('Installing twig/twig (v3.3.2)', $output);
        $this->assertStringContainsString('Hello world', $output);
    }

    public function testRunWithShebang()
    {
        $output = $this->melodyRunFixture('shebang.php');
        $this->assertStringContainsString('Loading composer repositories with package information', $output);
        $this->assertStringContainsString('Updating dependencies', $output);
        $this->assertStringContainsString('Installing twig/twig (v3.3.2)', $output);
        $this->assertStringNotContainsString('#!/usr/bin/env -S melody run', $output);
        $this->assertStringContainsString('Hello world', $output);
    }

    public function testRunWithCache()
    {
        $this->melodyRunFixture('hello-world.php');
        $output = $this->melodyRunFixture('hello-world.php');
        $this->assertSame('Hello world', $output);
    }

    public function testRunWithConstraints()
    {
        $output = $this->melodyRunFixture('hello-world-with-constraints.php');
        $this->assertStringContainsString('Hello world', $output);
    }

    public function testRunWithNoCache()
    {
        $this->melodyRunFixture('hello-world.php');
        $output = $this->melodyRunFixture('hello-world.php', array('no_cache' => true));
        $this->assertStringContainsString('Loading composer repositories with package information', $output);
        $this->assertStringContainsString('Updating dependencies', $output);
        $this->assertStringContainsString('Installing twig/twig (v3.3.2)', $output);
        $this->assertStringContainsString('Hello world', $output);
    }

    public function testRunWithPreferSource()
    {
        $output = $this->melodyRunFixture('pimple.php', array('prefer_source' => true));
        $this->assertStringContainsString('Loading composer repositories with package information', $output);
        $this->assertStringContainsString('Updating dependencies', $output);
        $this->assertStringContainsString('Installing pimple/pimple (v1.0.2)', $output);
        $this->assertStringContainsString('Cloning', $output);
        $this->assertStringContainsString('value', $output);
    }

    public function provideStreams()
    {
        return array(
            array('data', 'text/plain;base64,'.base64_encode(file_get_contents(__DIR__.'/Fixtures/hello-world.php'))),
        );
    }

    /**
     * @dataProvider provideStreams
     */
    public function testRunStream($protocol, $fixture)
    {
        $output = $this->melodyRunStream($protocol, $fixture, array('trust' => true));
        $this->assertStringContainsString('Hello world', $output);
    }

    public function testRunWithPhpOptions()
    {
        $output = $this->melodyRunFixture('php-options.php');
        $this->assertStringContainsString('memory_limit=42M', $output);
    }

    /**
     * @dataProvider provideGists
     */
    public function testRunGist($gist)
    {
        $output = $this->melodyRun($gist, array('trust' => true));
        $this->assertStringContainsString('Hello greg', $output);
    }

    public function provideGists()
    {
        return array(
            array('3e22492d1bfb05c80194e829055d07bc'),
            array('mykiwi/3e22492d1bfb05c80194e829055d07bc'),
            array('https://gist.github.com/mykiwi/3e22492d1bfb05c80194e829055d07bc'),
        );
    }

    public function testRunGistUntrusted()
    {
        $this->expectException('SensioLabs\Melody\Exception\TrustException');

        $this->melodyRun('3e22492d1bfb05c80194e829055d07bc', array('trust' => false));
    }

    public function testRunWithForkRepositories()
    {
        $output = $this->melodyRunFixture('fork-repositories.php', array('prefer_source' => true));

        $this->assertStringContainsString('Loading composer repositories with package information', $output);
        $this->assertStringContainsString('Updating dependencies', $output);
        $this->assertStringContainsString('Installing pimple/pimple (v1.0.2)', $output);
        $this->assertStringContainsString('Cloning', $output);
        $this->assertStringContainsString('value', $output);
    }

    private function melodyRunFixture($fixture, array $options = array())
    {
        return $this->melodyRun(sprintf('%s/Fixtures/%s', __DIR__, $fixture), $options);
    }

    private function melodyRunStream($protocol, $fixture, array $options = array())
    {
        $melody = new Melody();

        $filename = sprintf('%s://%s', $protocol, $fixture);

        $options = array_replace(array(
            'trust' => false,
            'prefer_source' => false,
            'no_cache' => false,
        ), $options);

        $runConfiguration = new RunConfiguration($options['no_cache'], $options['prefer_source'], $options['trust']);
        $userConfiguration = new UserConfiguration();

        $output = null;
        $cliExecutor = function (Process $process, $useProcessHelper) use (&$output) {
            $process->setTty(false);
            $process->mustRun(function ($type, $text) use (&$output) {
                $output .= $text;
            });
        };

        $melody->run($filename, array(), $runConfiguration, $userConfiguration, $cliExecutor);

        return $output;
    }

    private function melodyRun($filename, array $options = array())
    {
        $melody = new Melody();

        $options = array_replace(array(
            'trust' => false,
            'prefer_source' => false,
            'no_cache' => false,
        ), $options);

        $runConfiguration = new RunConfiguration($options['no_cache'], $options['prefer_source'], $options['trust']);
        $userConfiguration = new UserConfiguration();

        $output = null;
        $cliExecutor = function (Process $process, $useProcessHelper) use (&$output) {
            $process->setTty(false);
            $process->mustRun(function ($type, $text) use (&$output) {
                $output .= $text;
            });
        };

        $melody->run($filename, array(), $runConfiguration, $userConfiguration, $cliExecutor);

        return $output;
    }

    private function cleanCache()
    {
        $this->fs->remove(sys_get_temp_dir().'/melody');
    }

    private function getFixtureFile($fixtureName)
    {
        return sprintf('%s/Fixtures/%s', __DIR__, $fixtureName);
    }
}
