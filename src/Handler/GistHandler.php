<?php

namespace SensioLabs\Melody\Handler;

use SensioLabs\Melody\Handler\Github\Gist;
use SensioLabs\Melody\Resource\Resource;
use SensioLabs\Melody\Resource\Metadata;

/**
 * @author Charles Sarrazin <charles@sarraz.in>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 * @author Jérémy Derussé <jeremy@derusse.com>
 */
class GistHandler implements ResourceHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($uri)
    {
        return 0 !== preg_match(Gist::URI_PATTERN, $uri);
    }

    /**
     * {@inheritdoc}
     */
    public function createResource($uri)
    {
        $gist = new Gist($uri);
        $data = $gist->get();

        if (array_key_exists('message', $data)) {
            throw new \InvalidArgumentException('There is an issue with your gist URL: '.$data['message']);
        }

        $files = $data['files'];

        // Throw an error if the gist contains multiple files
        if (1 !== count($files)) {
            throw new \InvalidArgumentException('The gist should contain a single file');
        }

        // Fetch the only element in the array
        $file = current($files);
        $metadata = new Metadata(
            $data['id'],
            $data['owner']['login'],
            new \DateTime($data['created_at']),
            new \DateTime($data['updated_at']),
            count($data['history']),
            $data['html_url']
        );

        return new Resource($file['content'], $metadata);
    }
}
