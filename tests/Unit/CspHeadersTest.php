<?php

use PHPUnit\Framework\TestCase;

class CspHeadersTest extends TestCase
{
    private Hhcspheaders $module;
    private ReflectionMethod $getCspHeaders;

    protected function setUp(): void
    {
        Configuration::resetForTests();
        $this->module = new Hhcspheaders();
        $this->getCspHeaders = new ReflectionMethod($this->module, 'getCspHeaders');
        $this->getCspHeaders->setAccessible(true);
    }

    private function buildCspHeader(array $values): string
    {
        foreach ($values as $key => $value) {
            Configuration::set('HHCSPHEADERS_' . $key, $value);
        }

        return $this->getCspHeaders->invoke($this->module);
    }

    public function testEmptyConfigReturnsEmptyString(): void
    {
        $this->assertSame('', $this->buildCspHeader([]));
    }

    public function testEmptyValueIsIgnored(): void
    {
        $this->assertSame('', $this->buildCspHeader(['CSP_DEFAULT_SRC' => '']));
    }

    public function testWhitespaceOnlyValueIsIgnored(): void
    {
        $this->assertSame('', $this->buildCspHeader(['CSP_DEFAULT_SRC' => '   ']));
    }

    public function testNonCspKeysAreIgnored(): void
    {
        $result = $this->buildCspHeader([
            'ENABLE_FRONT' => '1',
            'ENABLE_BACK' => '1',
            'MODE' => 'BLOCK',
        ]);
        $this->assertSame('', $result);
    }

    public function testDefaultSrcDirective(): void
    {
        $result = $this->buildCspHeader(['CSP_DEFAULT_SRC' => "'self'"]);
        $this->assertStringContainsString("default-src 'self'", $result);
    }

    public function testScriptSrcDirective(): void
    {
        $result = $this->buildCspHeader(['CSP_SCRIPT_SRC' => "'self'"]);
        $this->assertStringContainsString("script-src 'self'", $result);
    }

    public function testStyleSrcDirective(): void
    {
        $result = $this->buildCspHeader(['CSP_STYLE_SRC' => "'self' 'unsafe-inline'"]);
        $this->assertStringContainsString("style-src 'self' 'unsafe-inline'", $result);
    }

    public function testImgSrcDirective(): void
    {
        $result = $this->buildCspHeader(['CSP_IMG_SRC' => '*']);
        $this->assertStringContainsString('img-src *', $result);
    }

    public function testFontSrcDirective(): void
    {
        $result = $this->buildCspHeader(['CSP_FONT_SRC' => "'self'"]);
        $this->assertStringContainsString("font-src 'self'", $result);
    }

    public function testConnectSrcDirective(): void
    {
        $result = $this->buildCspHeader(['CSP_CONNECT_SRC' => "'self'"]);
        $this->assertStringContainsString("connect-src 'self'", $result);
    }

    public function testObjectSrcDirective(): void
    {
        $result = $this->buildCspHeader(['CSP_OBJECT_SRC' => "'none'"]);
        $this->assertStringContainsString("object-src 'none'", $result);
    }

    public function testMediaSrcDirective(): void
    {
        $result = $this->buildCspHeader(['CSP_MEDIA_SRC' => "'self'"]);
        $this->assertStringContainsString("media-src 'self'", $result);
    }

    public function testFrameSrcDirective(): void
    {
        $result = $this->buildCspHeader(['CSP_FRAME_SRC' => "'self'"]);
        $this->assertStringContainsString("frame-src 'self'", $result);
    }

    public function testMultipleDirectivesAreAllIncluded(): void
    {
        $result = $this->buildCspHeader([
            'CSP_DEFAULT_SRC' => "'self'",
            'CSP_SCRIPT_SRC' => "'self'",
            'CSP_STYLE_SRC' => "'self' 'unsafe-inline'",
            'CSP_IMG_SRC' => '*',
        ]);

        $this->assertStringContainsString("default-src 'self'", $result);
        $this->assertStringContainsString("script-src 'self'", $result);
        $this->assertStringContainsString("style-src 'self' 'unsafe-inline'", $result);
        $this->assertStringContainsString('img-src *', $result);
    }

    public function testDirectivesAreSemicolonSeparated(): void
    {
        $result = $this->buildCspHeader([
            'CSP_DEFAULT_SRC' => "'self'",
            'CSP_SCRIPT_SRC' => "'self'",
        ]);

        $this->assertStringContainsString(';', $result);
    }

    public function testConfigurationKeyPrefixIsHhcspheaders(): void
    {
        $reflection = new ReflectionMethod($this->module, 'getConfigurationKeysWithPrefix');
        $reflection->setAccessible(true);
        $keys = $reflection->invoke($this->module);

        foreach ($keys as $key) {
            $this->assertStringStartsWith('HHCSPHEADERS_', $key);
        }
    }

    public function testAllConfigFieldsArePresent(): void
    {
        $reflection = new ReflectionMethod($this->module, 'getConfigurationKeysWithPrefix');
        $reflection->setAccessible(true);
        $keys = $reflection->invoke($this->module);

        $this->assertContains('HHCSPHEADERS_ENABLE_FRONT', $keys);
        $this->assertContains('HHCSPHEADERS_ENABLE_BACK', $keys);
        $this->assertContains('HHCSPHEADERS_MODE', $keys);
        $this->assertContains('HHCSPHEADERS_CSP_DEFAULT_SRC', $keys);
        $this->assertContains('HHCSPHEADERS_ENABLE_XFRAME', $keys);
        $this->assertContains('HHCSPHEADERS_ENABLE_REFERRER', $keys);
        $this->assertCount(18, $keys);
    }
}
