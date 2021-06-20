<?php

namespace SensioLabs\Melody\Handler;

use SensioLabs\Melody\Resource\Metadata;
use SensioLabs\Melody\Resource\Resource;

/**
 * @author Jérémy Derussé <jeremy@derusse.com>
 */
class StreamHandler implements ResourceHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($filename)
    {
        $opened = @fopen($filename, 'r');

        return false !== $opened;
    }

    /**
     * {@inheritdoc}
     */
    public function createResource($filename)
    {
        $metadata = new Metadata(
            basename($filename),
            null,
            new \DateTime(),
            new \DateTime(),
            1,
            $filename
        );

        return new Resource(file_get_contents($filename), $metadata);
    }
}
