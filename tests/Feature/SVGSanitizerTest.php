<?php
use Tests\TestCase;
use enshrined\svgSanitize\Sanitizer;

class SVGSanitizerTest extends TestCase
{
    public function testSvgSanitization()
    {
        $sanitizer = new Sanitizer();
        $maliciousSvg = '<svg><script>alert("XSS")</script></svg>';
        $sanitizedSvg = $sanitizer->sanitize($maliciousSvg);

        $this->assertStringNotContainsString('<script>', $sanitizedSvg);
    }

    public function testValidSvgSanitization()
    {
        $sanitizer = new Sanitizer();
        $validSvg = '<svg><circle cx="50" cy="50" r="40" stroke="black" stroke-width="3" fill="red" /></svg>';
        $sanitizedSvg = $sanitizer->sanitize($validSvg);

        $this->assertStringContainsString('<circle', $sanitizedSvg);
    }
}