<?php

namespace SensioLabs\Melody\Resource;

/**
 * @author Jérémy Derussé <jeremy@derusse.com>
 */
class LocalResource extends Resource
{
    private $filename;

    public function __construct($filename, $content, Metadata $metadata)
    {
        parent::__construct($content, $metadata);
        $this->filename = $filename;
    }

    public function getFilename()
    {
        return $this->filename;
    }
}
