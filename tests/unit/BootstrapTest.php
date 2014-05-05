<?php

use Intersvyaz\Bootstrap\Bootstrap;

/**
 * @coversDefaultClass \Intersvyaz\Bootstrap\Bootstrap
 */
class BootstrapTest extends \PHPUnit_Framework_TestCase
{
	const TEST_CLASS = '\Intersvyaz\Bootstrap\Bootstrap';

	/**
	 * @covers ::init
	 */
	public function testInit()
	{
		$prop = new ReflectionProperty(self::TEST_CLASS, 'assetsPath');
		$prop->setAccessible(true);

		$bs = new Bootstrap();
		$bs->init();
		$this->assertEquals(realpath(__DIR__ . '/../../assets'), $prop->getValue($bs));

		$bs = new Bootstrap();
		$bs->assetsAlias = 'fakes.assets';
		$bs->init();
		$this->assertEquals(Yii::getPathOfAlias('fakes.assets'), $prop->getValue($bs));
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
		$cs = Yii::app()->clientScript;
		$cs->reset();

		$bs = new Bootstrap();
		$bs->minifyAssets = $minify;
		$bs->responsive = $responsive;
		$bs->init();
		$return = $bs->registerStyleAssets();
		$this->assertInstanceOf(get_class($bs), $return);

		$assetPath = TestHelper::getPropValue($bs, 'assetsPath');
		$assetUrl = \Yii::app()->assetManager->publish($assetPath);

		foreach ($cssFiles as $cssFile) {
			$this->assertTrue($cs->isCssFileRegistered($assetUrl . $cssFile));
		}

		if ($responsive) {
			$prop = new ReflectionProperty(get_class($cs), 'metaTags');
			$prop->setAccessible(true);
			$metaTags = $prop->getValue($cs);
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
		$cs = \Yii::app()->clientScript;
		$cs->reset();

		$bs = new Bootstrap();
		$bs->minifyAssets = $minify;
		$bs->init();
		$return = $bs->registerScriptAssets();
		$this->assertInstanceOf(get_class($bs), $return);

		$assetPath = TestHelper::getPropValue($bs, 'assetsPath');
		$assetUrl = \Yii::app()->assetManager->publish($assetPath);

		foreach ($jsFiles as $jsFile) {
			$this->assertTrue($cs->isScriptFileRegistered($assetUrl . $jsFile, $cs->defaultScriptFilePosition));
		}
	}
}
