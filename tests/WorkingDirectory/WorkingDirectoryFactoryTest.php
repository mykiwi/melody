<?php

namespace SensioLabs\Melody\Tests\WorkingDirectory;

use PHPUnit\Framework\TestCase;
use SensioLabs\Melody\WorkingDirectory\WorkingDirectoryFactory;
use org\bovigo\vfs\vfsStream;

class WorkingDirectoryFactoryTest extends TestCase
{
    /** @var \org\bovigo\vfs\vfsStreamDirectory */
    private $storageDir;
    /** @var \SensioLabs\Melody\WorkingDirectory\WorkingDirectoryFactory */
    private $workingDirectoryFactory;

    protected function setUp(): void
    {
        $this->storageDir = vfsStream::setup('storageDir');
        $this->workingDirectoryFactory = new WorkingDirectoryFactory($this->storageDir->url());
    }

    public function testCreateTmpDir()
    {
        $workingDirectory = $this->workingDirectoryFactory->createTmpDir(array(), array());

        $workingDirectoryPath = $workingDirectory->getPath();
        $this->assertNotEquals($this->storageDir->url(), $workingDirectoryPath);
        $this->assertEquals($this->storageDir->url(), dirname($workingDirectoryPath));
        $this->assertNotEmpty(basename($workingDirectoryPath));
    }

    public static function sameConfigProvider()
    {
        return array(
            'no_config' => array(
                array(), array(),
                array(), array(),
            ),
            'one_package' => array(
                array('symfony/symfony' => '*'), array(),
                array('symfony/symfony' => '*'), array(),
            ),
            'packages_order' => array(
                array(
                    'symfony/symfony' => '*',
                    'sensiolabs/melody' => '*',
                ),
                array(),
                array(
                    'sensiolabs/melody' => '*',
                    'symfony/symfony' => '*',
                ),
                array(),
            ),
            'repositories_order' => array(
                array(
                    'symfony/symfony' => '*',
                    'sensiolabs/melody' => '*',
                ),
                array(
                    array(
                        'type' => 'vcs',
                        'url' => 'https://example.com/symfony',
                    ),
                    array(
                        'type' => 'vcs',
                        'url' => 'https://example.com/melody',
                    ),
                ),
                array(
                    'symfony/symfony' => '*',
                    'sensiolabs/melody' => '*',
                ),
                array(
                    array(
                        'url' => 'https://example.com/melody',
                        'type' => 'vcs',
                    ),
                    array(
                        'url' => 'https://example.com/symfony',
                        'type' => 'vcs',
                    ),
                ),
            ),
            'packages_repositories_order' => array(
                array(
                    'symfony/symfony' => '*',
                    'sensiolabs/melody' => '*',
                    'pear-pear2.php.net/PEAR2_Text_Markdown' => '*',
                    'smarty/smarty' => '*',
                ),
                array(
                    array(
                        'type' => 'vcs',
                        'url' => 'https://example.com/symfony',
                    ),
                    array(
                        'type' => 'vcs',
                        'url' => 'https://example.com/melody',
                    ),
                    array(
                        'type' => 'package',
                        'package' => array(
                        'name' => 'smarty/smarty',
                            'version' => '3.1.7',
                            'dist' => array(
                                'url' => 'https://www.smarty.net/files/Smarty-3.1.7.zip',
                                'type' => 'zip',
                            ),
                            'source' => array(
                                'url' => 'https://smarty-php.googlecode.com/svn/',
                                'type' => 'svn',
                                'reference' => 'tags/Smarty_3_1_7/distribution/',
                            ),
                        ),
                    ),
                    array(
                        'type' => 'pear',
                        'url' => 'http://pear2.php.net',
                    ),
                ),
                array(
                    'symfony/symfony' => '*',
                    'sensiolabs/melody' => '*',
                    'smarty/smarty' => '*',
                    'pear-pear2.php.net/PEAR2_Text_Markdown' => '*',
                ),
                array(
                    array(
                        'type' => 'package',
                        'package' => array(
                            'name' => 'smarty/smarty',
                            'version' => '3.1.7',
                            'source' => array(
                                'url' => 'https://smarty-php.googlecode.com/svn/',
                                'type' => 'svn',
                                'reference' => 'tags/Smarty_3_1_7/distribution/',
                            ),
                            'dist' => array(
                                'url' => 'https://www.smarty.net/files/Smarty-3.1.7.zip',
                                'type' => 'zip',
                            ),
                        ),
                    ),
                    array(
                        'url' => 'https://example.com/melody',
                        'type' => 'vcs',
                    ),
                    array(
                        'type' => 'pear',
                        'url' => 'http://pear2.php.net',
                    ),
                    array(
                        'url' => 'https://example.com/symfony',
                        'type' => 'vcs',
                    ),
                ),
            ),
        );
    }

    /**
     * @dataProvider sameConfigProvider
     */
    public function testCreateTmpDirWithSameConfigShouldNotChange($packages, $repositories, $otherPackages, $otherRepositories)
    {
        $this->assertEquals(
            $this->workingDirectoryFactory->createTmpDir($packages, $repositories)->getPath(),
            $this->workingDirectoryFactory->createTmpDir($otherPackages, $otherRepositories)->getPath()
        );
    }

    public static function differentConfigProvider()
    {
        return array(
            array(
                array(), array(),
                array('symfony/symfony' => '*'), array(),
            ),
            array(
                array('sensiolabs/melody' => '*'),
                array(),
                array('sensiolabs/melody' => '*'),
                array(
                    array(
                        'type' => 'vcs',
                        'url' => 'https://example.com/melody',
                    ),
                ),
            ),
            array(
                array('sensiolabs/melody' => '*'),
                array(
                    array(
                        'type' => 'vcs',
                        'url' => 'https://example.com/melody',
                    ),
                ),
                array('sensiolabs/melody' => '*'),
                array(
                    array(
                        'type' => 'vcs',
                        'url' => 'https://otherexample.com/melody',
                    ),
                ),
            ),
        );
    }

    /**
     * @dataProvider differentConfigProvider
     */
    public function testCreateTmpDirWithDifferentConfigShouldChange($packages, $repositories, $otherPackages, $otherRepositories)
    {
        $this->assertNotEquals(
            $this->workingDirectoryFactory->createTmpDir($packages, $repositories)->getPath(),
            $this->workingDirectoryFactory->createTmpDir($otherPackages, $otherRepositories)->getPath()
        );
    }
}
