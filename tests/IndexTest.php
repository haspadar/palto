<?php

use PHPUnit\Framework\TestCase;

class IndexTest extends WebTest
{
    public function testIndex()
    {
        $content = $this->download('http://localhost:8002');
        \Palto\Debug::dump($content);
        \Palto\Debug::dump(str_contains($content, 'Warning: '));exit;
        $this->assertFalse(str_contains($content, 'Warning: '));
    }
}