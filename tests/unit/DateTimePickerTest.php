<?php

use \Intersvyaz\YayBootstrap\DateTimePicker;
use \Intersvyaz\YayBootstrap\Bootstrap;

/**
 * @coversDefaultClass \Intersvyaz\YayBootstrap\DateTimePicker
 */
class DateTimePickerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @return DateTimePicker
	 */
	public function makeWidget()
	{
		return new DateTimePicker();
	}

	/**
	 * @covers ::init
	 */
	public function testInit()
	{
		$widget = $this->makeWidget();
		$widget->init();
		$this->assertArrayHasKey('language', $widget->options);
		$this->assertNotEmpty($widget->options['language']);
	}

	/**
	 * @covers ::run
	 */
	public function testRun_FieldRenderingWithAttribute()
	{
		$widget = $this->makeWidget();
		$widget->init();
		$widget->name = 'attr';
		$widget->value = 'val';
		$widget->htmlOptions = ['foo' => 'bar'];

		$widgetOutput = WidgetTestHelper::runAndCapture($widget);
		$this->assertTag([
			'tag' => 'input',
			'attributes' => [
				'type' => 'text',
				'foo' => 'bar',
				'name' => 'attr',
				'value' => 'val',
				'id' => 'attr'
			]
		], $widgetOutput);

		// custom input id attribute
		$widget->htmlOptions['id'] = 'baz';
		$widgetOutput = WidgetTestHelper::runAndCapture($widget);
		$this->assertTag([
			'tag' => 'input',
			'attributes' => [
				'foo' => 'bar',
				'id' => 'baz'
			]
		], $widgetOutput);
	}

	/**
	 * @covers ::run
	 */
	public function testRun_FieldRenderingWithModel()
	{
		$widget = $this->makeWidget();
		$widget->init();
		$widget->model = new FakeModel();
		$widget->attribute = 'login';
		$widget->model->login = 'val';
		$widget->htmlOptions = ['foo' => 'bar'];

		$widgetOutput = WidgetTestHelper::runAndCapture($widget);
		$this->assertTag([
			'tag' => 'input',
			'attributes' => [
				'type' => 'text',
				'foo' => 'bar',
				'name' => 'FakeModel[login]',
				'value' => 'val',
				'id' => 'FakeModel_login',
			]
		], $widgetOutput);

		// custom id and name attributes
		$widget->htmlOptions['name'] = 'alpha';
		$widget->htmlOptions['id'] = 'beta';
		$widgetOutput = WidgetTestHelper::runAndCapture($widget);
		$this->assertTag([
			'tag' => 'input',
			'attributes' => [
				'foo' => 'bar',
				'name' => 'alpha',
				'id' => 'beta',
			]
		], $widgetOutput);
	}

	/**
	 * @covers ::run
	 */
	public function testScriptGeneration()
	{
		$widget = $this->makeWidget();
		$widget->name = 'foo';
		$widget->value = 'val';
		$widget->options = ['language' => 'ru', 'foo' => 'bar'];
		$widget->events = ['click' => 'return foobar()'];
		$widget->init();
		ob_start();
		$widget->run();
		ob_end_clean();
		$cs = Bootstrap::instance()->clientScript;
		$script = $cs->scripts[$cs->defaultScriptPosition][get_class($widget) . '#' . $widget->getId()];
		$this->assertEquals(
			"jQuery('#foo').datetimepicker({'language':'ru','foo':'bar'}).on('click','return foobar()');",
			$script
		);
	}

	public function registerAssetsProvider()
	{
		return [
			[true, '.min'],
			[false, ''],
		];
	}

	/**
	 * @param bool $minify
	 * @param string $minifyPrefix
	 * @dataProvider registerAssetsProvider
	 * @covers ::registerAssets
	 */
	public function testRegisterAssets($minify, $minifyPrefix)
	{
		$cs = Bootstrap::instance()->clientScript;
		$cs->reset();
		Bootstrap::instance()->minifyAssets = $minify;
		$widget = $this->makeWidget();
		$widget->name = 'foo';
		$widget->value = 'val';
		$widget->options = ['language' => 'ru'];
		$widget->init();

		// non-minifed assets
		ob_start();
		$widget->run();
		ob_end_clean();
		$assetPath = Bootstrap::instance()->resolveAssetPath($widget::ASSET_NAME);
		$assetUrl = Bootstrap::instance()->assetManager->publish($assetPath);

		$this->assertTrue(
			$cs->isCssFileRegistered($assetUrl . '/bootstrap-datetimepicker' . $minifyPrefix . '.css')
		);
		$this->assertTrue(
			$cs->isScriptFileRegistered(
				$assetUrl . '/bootstrap-datetimepicker' . $minifyPrefix . '.js',
				$cs->defaultScriptFilePosition
			)
		);
		$this->assertTrue(
			$cs->isScriptFileRegistered(
				$assetUrl . '/locales/bootstrap-datetimepicker.ru.js',
				$cs->defaultScriptFilePosition
			)
		);
	}
}