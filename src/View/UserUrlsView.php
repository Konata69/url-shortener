<?php

namespace App\View;

use App\Entity\Url;
use App\Service\Shortener;

class UserUrlsView
{
    private $urls;
    private $shortener;

    public function __construct(Shortener $shortener)
    {
        $this->shortener = $shortener;
    }

    public function asArray(): array
    {
        $result = [];

        /** @var Url $url */
        foreach ($this->urls as $url) {
            $result[] = [
                'url' => $url->getUrl(),
                'shortUrl' => $this->shortener->getFollowUrl($url->getHash()),
            ];
        }

        return $result;
    }

    public function setUrls($urls): void
    {
        $this->urls = $urls;
    }
}