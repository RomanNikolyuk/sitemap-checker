<?php

namespace Vlst\SitemapChecker;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use RuntimeException;
use XMLReader;

class SitemapClient
{
    protected string $url;

    protected Closure $onError;

    protected Closure $onFinished;

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function setOnError(callable $callback): static
    {
        $this->onError = $callback;

        return $this;
    }

    public function setOnFinished(callable $callback): static
    {
        $this->onFinished = $callback;

        return $this;
    }

    public function check(): void
    {
        $sitemapPath = $this->downloadSitemap();

        $this->parseSitemap($sitemapPath);
    }

    public function downloadSitemap(): string
    {
        $tmpDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR;

        if (!is_dir($tmpDir)) {
            if (!mkdir($tmpDir, 0777, true) && !is_dir($tmpDir)) {
                throw new \RuntimeException("Failed to create directory: $tmpDir");
            }
        }

        $client = new Client();

        $response = $client->get($this->url, [
            RequestOptions::SINK => $tmpDir  . 'sitemap.xml',
        ]);

        $contentType = $response->getHeader('Content-Type')[0];

        if (! in_array($contentType, ['text/xml', 'application/xml'])) {
            throw new RuntimeException('Unexpected sitemap format. Expecting "application/xml" or "text/xml", got "' . $contentType . '"');
        }

        return $tmpDir  . 'sitemap.xml';
    }

    protected function parseSitemap(string $sitemapPath): void
    {
        $reader = new XMLReader();

        if (! $reader->open($sitemapPath)) {
            throw new RuntimeException('Failed to open sitemap file for parsing');
        }

        while ($reader->read() && $reader->nodeType !== XMLReader::ELEMENT) {}

        $isSitemapIndex = false;
        while ($reader->read()) {
            $link = null;

            if ($reader->nodeType === XMLReader::ELEMENT) {
                if ($reader->name === 'sitemap') {
                    $isSitemapIndex = true;
                }

                if ($reader->name === 'loc') {
                    $link = $reader->readString();

                    if ($isSitemapIndex) {
                        (clone $this)->setUrl($link)->check();
                    }
                } elseif ($reader->name === 'xhtml:link') {
                    $link = $reader->getAttribute('href');
                }
            }

            if ($link) {
                $response = (new Client())->get($link);

                if ($response->getStatusCode() !== 200) {
                    ($this->onError)($link, $response->getStatusCode());
                }
            }
        }

        ($this->onFinished)();
    }
}
