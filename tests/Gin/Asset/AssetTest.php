<?php

namespace Tonik\Gin\Asset;

use Mockery;
use TestCase;
use Tonik\Gin\Asset\Asset;
use Brain\Monkey\Functions;
use phpmock\phpunit\PHPMock;
use Tonik\Gin\Foundation\Config;
use Tonik\Gin\Foundation\Exception\FileNotFoundException;

class AssetTest extends TestCase
{
    use PHPMock;

    /**
     * @test
     */
    public function test_file_setter_and_getter()
    {
        $config = $this->getConfig();
        $asset = $this->getAsset($config, 'js/sample-asset.js');

        $this->assertEquals($asset->getFile(), 'js/sample-asset.js');
    }

    /**
     * @test
     */
    public function test_relative_path_getter()
    {
        $config = $this->getConfig();
        $asset = $this->getAsset($config, 'js/sample-asset.js');

        $config->shouldReceive('offsetGet')
            ->with('directories')
            ->andReturn(['public' => 'public']);

        $this->assertEquals($asset->getRelativePath(), 'public/js/sample-asset.js');
    }

    /**
     * @test
     */
    public function test_public_path_getter()
    {
        $config = $this->getConfig();
        $asset = $this->getAsset($config, 'js/sample-asset.js');

        $config->shouldReceive('offsetGet')
            ->with('paths')
            ->andReturn(['directory' => 'abs/path']);

        $config->shouldReceive('offsetGet')
            ->with('directories')
            ->andReturn(['public' => 'public']);

        $this->assertEquals($asset->getPublicPath(), 'abs/path/public/js/sample-asset.js');
    }

    /**
     * @test
     */
    public function test_public_uri_getter()
    {
        $config = $this->getConfig();
        $asset = $this->getAsset($config, 'js/sample-asset.js');

        $config->shouldReceive('offsetGet')
            ->with('paths')
            ->andReturn(['uri' => 'uri/path']);

        $config->shouldReceive('offsetGet')
            ->with('directories')
            ->andReturn(['public' => 'public']);

        $this->assertEquals($asset->getPublicUri(), 'uri/path/public/js/sample-asset.js');
    }

    /**
     * @test
     */
    public function test_uri_getter()
    {
        $config = $this->getConfig();
        $asset = $this->getAsset($config, 'js/sample-asset.js');

        $exists = $this->getFunctionMock(__NAMESPACE__, "file_exists");
        $exists->expects($this->once())->willReturn(true);

        $config->shouldReceive('offsetGet')
            ->with('paths')
            ->andReturn([
                'directory' => 'abs/path',
                'uri' => 'uri/path'
            ]);

        $config->shouldReceive('offsetGet')
            ->with('directories')
            ->andReturn(['public' => 'public']);

        $this->assertEquals($asset->getUri(), 'uri/path/public/js/sample-asset.js');
    }

    /**
     * @test
     */
    public function it_should_throw_on_file_if_no_asset_file()
    {
        $config = $this->getConfig();
        $asset = $this->getAsset($config, 'js/sample-asset.js');

        $config->shouldReceive('offsetGet')
            ->with('paths')
            ->andReturn(['directory' => 'abs/path']);

        $config->shouldReceive('offsetGet')
            ->with('directories')
            ->andReturn(['public' => 'public']);

        $this->expectException(FileNotFoundException::class);

        $asset->getUri();
    }

    public function getConfig()
    {
        return Mockery::mock(Config::class, [
            'paths' => [
                'uri' => 'uri/path',
            ],
            'directories' => [
                'assets' => 'resources/assets'
            ]
        ]);
    }

    public function getAsset($config, $name)
    {
        return (new Asset($config))->setFile($name);
    }
}