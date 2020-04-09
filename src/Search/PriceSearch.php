<?php

declare(strict_types = 1);

namespace Search;

use function Clue\React\Block\awaitAll;
use Clue\React\Buzz\Browser;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Factory;

class PriceSearch
{
    private const BASE_URL = 'https://www.autoscout24.de';

    private $brand;
    private $model;
    private $pages;
    private $params;
    private $async;

    public function __construct(string $brand, string $model, int $pages = 1, array $params = [], $async=false)
    {
        $this->brand = $brand;
        $this->model = $model;
        $this->pages = $pages;
        $this->params = $params;
        $this->async = $async;
    }

    public function getAveragePrice($async): float
    {
        $sum = 0;
        $nodes = 0;

        foreach ($this->getSearchResults($async) as $nodeList) {
            foreach ($nodeList as $node) {
                $nodes++;
                $sum += (int) filter_var($node->nodeValue, FILTER_SANITIZE_NUMBER_INT);
            }
        }

        return $sum / $nodes;
    }

    private function getSearchResults(): array
    {
        $urls = $this->getSearchUrls();

        return $this->async ? $this->getResultsAsync($urls) : $this->getResultsSync($urls);
    }

    private function getResultsSync(array $urls): array
    {
        $results = [];

        foreach ($urls as $url) {
            $dom = new \DOMDocument();
            @$dom->loadHTMLFile($url);
            $xpath = new \DOMXPath($dom);

            $results[] = $this->getQuery($xpath);
        }

        return $results;
    }

    private function getResultsAsync(array $urls): array
    {
        $results = [];

        $loop = Factory::create();
        $browser = new Browser($loop);
        $promises = [];

        foreach ($urls as $url) {
            $promises[] = $browser->get($url);
        }

        $responses = awaitAll($promises, $loop);

        /** @var ResponseInterface $response */
        foreach ($responses as $response) {
            $dom = new \DOMDocument();
            @$dom->loadHTML((string) $response->getBody());
            $xpath = new \DOMXPath($dom);

            $results[] = $this->getQuery($xpath);
        }

        return $results;
    }

    private function getQuery(\DOMXPath $xpath): \DOMNodeList
    {
        return $xpath->query('//span[@data-item-name="price"]');
    }

    private function getSearchUrls(): array
    {
        $urls = [];

        for ($page = 1; $page <= $this->pages; $page++) {
            $this->params['page'] = $page;
            $searchUrl = sprintf(
                self::BASE_URL . '/lst/%s/%s?',
                $this->brand,
                $this->model
            );

            foreach ($this->params as $key => $value) {
                $searchUrl .= $key . '=' . $value . '&';
            }

            $urls[] = $searchUrl;
        }

        return $urls;
    }
}
