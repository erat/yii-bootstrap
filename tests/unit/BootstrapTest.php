<?php

use Intersvyaz\YayBootstrap\Bootstrap;

/**
 * @coversDefaultClass \Intersvyaz\YayBootstrap\Bootstrap
 */
class BootstrapTest extends \PHPUnit_Framework_TestCase
{
	const TEST_CLASS = '\Intersvyaz\YayBootstrap\Bootstrap';

	/**
	 * @covers ::__construct
	 * @covers ::instance
	 */
	public function testSingleton()
	{
		$instance = Bootstrap::instance();
		new Bootstrap();
		$this->assertEquals($instance, Bootstrap::instance());
	}

	/**
	 * @covers ::init
	 */
	public function testInit()
	{
		$property = new \ReflectionProperty(self::TEST_CLASS, 'defaultAssetsPath');
		$property->setAccessible(true);
		$instance = Bootstrap::instance();
		$instance->init();
		$defaultAssetPath = $property->getValue($instance);
		$this->assertFileExists($defaultAssetPath);
	}

	/**
	 * @covers ::resolveAssetPath
	 */
	public function testResolveAssetPath()
	{
		$instance = Bootstrap::instance();

		$property = new \ReflectionProperty(self::TEST_CLASS, 'defaultAssetsPath');
		$property->setAccessible(true);
		$defaultAssetPath = $property->getValue($instance);

		// default path
		$path = $instance->resolveAssetPath('bootstrap');
		$this->assertEquals($defaultAssetPath . DIRECTORY_SEPARATOR . 'bootstrap', $path);

		// fake global path
		$instance->assetsPath = 'fakes.assets';
		$path = $instance->resolveAssetPath('blah');
		$this->assertEquals($defaultAssetPath . DIRECTORY_SEPARATOR . 'blah', $path);

		// real global path
		$instance->assetsPath = 'fakes.assets';
		$path = $instance->resolveAssetPath('test');
		$this->assertEquals(\Yii::getPathOfAlias('fakes.assets.test'), $path);

		// fake separate path
		$instance->assetsPath = ['bootstrap' => 'fakes.assets.bootstrap'];
		try {
			$exceptionThrown = false;
			$path = $instance->resolveAssetPath('bootstrap');
		} catch (Exception $e) {
			$exceptionThrown = true;
		}
		$this->assertTrue($exceptionThrown);

		// real separate path
		$instance->assetsPath = ['test' => 'fakes.assets.test'];
		$path = $instance->resolveAssetPath('test');
		$this->assertEquals(\Yii::getPathOfAlias('fakes.assets.test'), $path);
	}

	public function registerStyleAssetsProvider()
	{
		return [
			[false, false, ['/css/bootstrap.css', '/css/bootstrap.yii.css']],
			[false, true, ['/css/bootstrap.responsive.css', '/css/bootstrap.yii.css']],
			[true, false, ['/css/bootstrap.min.css', '/css/bootstrap.yii.css']],
			[true, true, ['/css/bootstrap.responsive.min.css', '/css/bootstrap.yii.css']],
		];
	}

	/**
	 * @param bool $minify
	 * @param bool $responsive
	 * @param string[] $cssFiles
	 * @covers ::registerStyleAssets
	 * @dataProvider registerStyleAssetsProvider
	 */
	public function testRegisterStyleAssets($minify, $responsive, $cssFiles)
	{
		Bootstrap::instance()->clientScript->reset();
		Bootstrap::instance()->minifyAssets = $minify;
		Bootstrap::instance()->responsive = $responsive;
		$return = Bootstrap::instance()->registerStyleAssets();
		$this->assertInstanceOf(get_class(Bootstrap::instance()), $return);

		$assetPath = Bootstrap::instance()->resolveAssetPath(Bootstrap::ASSET_BOOTSTRAP);
		$assetUrl = Bootstrap::instance()->assetManager->publish($assetPath);

		foreach ($cssFiles as $cssFile) {
			$this->assertTrue(
				Bootstrap::instance()->clientScript
					->isCssFileRegistered($assetUrl . $cssFile)
			);
		}

		if ($responsive) {
			$prop = new ReflectionProperty(get_class(Bootstrap::instance()->clientScript), 'metaTags');
			$prop->setAccessible(true);
			$metaTags = $prop->getValue(Bootstrap::instance()->clientScript);
			$this->assertEquals('viewport', $metaTags[0]['name']);
		}
	}

	public function registerScriptAssetsProvider()
	{
		return [
			[false, ['/js/bootstrap.js']],
			[true, ['/js/bootstrap.min.js']],
		];
	}

	/**
	 * @param bool $minify
	 * @param string[] $jsFiles
	 * @covers ::registerScriptAssets
	 * @dataProvider registerScriptAssetsProvider
	 */
	public function testRegisterScriptAssets($minify, $jsFiles)
	{
		$cs = Bootstrap::instance()->clientScript;
		$cs->reset();
		Bootstrap::instance()->minifyAssets = $minify;
		$return = Bootstrap::instance()->registerScriptAssets();
		$this->assertInstanceOf(get_class(Bootstrap::instance()), $return);

		$assetPath = Bootstrap::instance()->resolveAssetPath(Bootstrap::ASSET_BOOTSTRAP);
		$assetUrl = Bootstrap::instance()->assetManager->publish($assetPath);

		foreach ($jsFiles as $jsFile) {
			$this->assertTrue($cs->isScriptFileRegistered($assetUrl . $jsFile, $cs->defaultScriptFilePosition));
		}
	}

	public function registerFontAwesomeAssetsProvider()
	{
		return [
			[false, ['/css/font-awesome.css']],
			[true, ['/css/font-awesome.min.css']],
		];
	}

	/**
	 * @param bool $minify
	 * @param string[] $cssFiles
	 * @covers ::registerFontAwesomeAssets
	 * @dataProvider registerFontAwesomeAssetsProvider
	 */
	public function testRegisterFontAwesomeAssets($minify, $cssFiles)
	{
		Bootstrap::instance()->clientScript->reset();
		Bootstrap::instance()->minifyAssets = $minify;
		$return = Bootstrap::instance()->registerFontAwesomeAssets();
		$this->assertInstanceOf(get_class(Bootstrap::instance()), $return);

		$assetPath = Bootstrap::instance()->resolveAssetPath(Bootstrap::ASSET_FONT_AWESOME);
		$assetUrl = Bootstrap::instance()->assetManager->publish($assetPath);

		foreach ($cssFiles as $cssFile) {
			$this->assertTrue(
				Bootstrap::instance()->clientScript
					->isCssFileRegistered($assetUrl . $cssFile)
			);
		}
	}
}