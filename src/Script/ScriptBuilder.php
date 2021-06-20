<?php

namespace SensioLabs\Melody\Script;

use SensioLabs\Melody\Configuration\RunConfigurationParser;
use SensioLabs\Melody\Resource\Resource;
use SensioLabs\Melody\Resource\ResourceParser;

/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class ScriptBuilder
{
    private $resourceParser;
    private $configurationParser;

    public function __construct()
    {
        $this->resourceParser = new ResourceParser();
        $this->configurationParser = new RunConfigurationParser();
    }

    public function buildScript(Resource $resource, array $arguments)
    {
        $config = $this->resourceParser->parseResource($resource);

        $configuration = $this->configurationParser->parseConfiguration($config);

        return new Script($resource, $arguments, $configuration);
    }
}
