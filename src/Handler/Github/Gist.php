<?php

namespace SensioLabs\Melody\Handler\Github;

/**
 * @author Charles Sarrazin <charles@sarraz.in>
 */
class Gist
{
    const URI_PATTERN = '#^(?:https://gist.github.com/)?(?:[a-zA-Z0-9][a-zA-Z0-9-]{0,38}\/)?([0-9a-f]+)$#';

    private $id;
    private $content;

    /**
     * Extracts a gist's information from a gist URL.
     *
     * @param string $url The gist's URL
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($url)
    {
        if (!preg_match(self::URI_PATTERN, $url, $matches)) {
            throw new \InvalidArgumentException(sprintf('"%s" does not seem to be a Gist URL.', $url));
        }

        $this->id = $matches[1];
    }

    public function get()
    {
        if (null === $this->content) {
            $this->content = $this->download();
        }

        return $this->content;
    }

    /**
     * Call Github and return JSON data.
     *
     * @return mixed<string> the content of API call to Github
     */
    public function download()
    {
        $handle = curl_init();
        $http_proxy = filter_input(INPUT_ENV, 'HTTPS_PROXY', FILTER_SANITIZE_URL);

        curl_setopt_array($handle, array(
            CURLOPT_URL => sprintf('https://api.github.com/gists/%s', $this->id),
            CURLOPT_HTTPHEADER => array(
                'Accept: application/vnd.github.v3+json',
                'User-Agent: Melody-Script',
            ),
            CURLOPT_RETURNTRANSFER => 1,
        ));

        if ($http_proxy) {
            curl_setopt($handle, CURLOPT_PROXY, $http_proxy);
        }

        $content = curl_exec($handle);
        curl_close($handle);

        if (!$content) {
            throw new \InvalidArgumentException(sprintf('Gist "%s" not found', $this->id));
        }

        return json_decode($content, true);
    }
}
