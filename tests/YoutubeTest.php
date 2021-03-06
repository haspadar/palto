<?php

namespace Test;


use Palto\Debug;

class YoutubeTest extends Web
{
    protected string $url = '/youtube.php?query=alfa+romeo+146+na+czesci+silnik+1%2C4+boxer';

    public function testJson()
    {
        $jsonResponse = $this->download($this->url);
        $this->assertTrue($jsonResponse->isJson());
        $this->assertTrue(strlen($jsonResponse->getJson()->video_id) > 0);
    }
}