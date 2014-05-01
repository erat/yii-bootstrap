<?php

use Intersvyaz\YayBootstrap\Alert;
use Intersvyaz\YayBootstrap\Bootstrap;

/**
 * @coversDefaultClass \Intersvyaz\YayBootstrap\Alert
 */
class AlertTest extends \PHPUnit_Framework_TestCase
{
	const WIDGET_CLASS = '\Intersvyaz\YayBootstrap\Alert';

	/**
	 * @return Alert
	 */
	public function makeWidget()
	{
		return new Alert();
	}

	/**
	 * @coversNothing
	 */
	public function testDefaultPropertyValues()
	{
		$widget = $this->makeWidget();
		$this->assertAttributeInternalType('array', 'alertOptions', $widget);
		$this->assertAttributeInternalType('array', 'events', $widget);
		$this->assertAttributeInternalType('array', 'htmlOptions', $widget);
		$this->assertAttributeInternalType('boolean', 'block', $widget);
		$this->assertAttributeInternalType('boolean', 'fade', $widget);
		$this->assertAttributeNotEmpty('alertTag', $widget);
		$this->assertAttributeNotEmpty('closeText', $widget);
		$this->assertAttributeNotEmpty('wrapperTag', $widget);
	}

	/**
	 * @covers ::init
	 */
	public function testInit()
	{
		$widget = $this->makeWidget();
		$widget->init();
		$this->assertEquals($widget->htmlOptions['id'], $widget->getId());
	}

	/**
	 * @covers ::run
	 */
	public function testRun_NoOutputOnNoAlerts()
	{
		$widget = $this->makeWidget();
		$data = WidgetTestHelper::runAndCapture($widget);
		$this->assertEmpty($data);
	}

	/**
	 * @covers ::run
	 */
	public function testRun_WrapperTag()
	{
		$widget = $this->makeWidget();
		$widget->alerts = ['foo' => 'bar'];
		$widget->htmlOptions = ['one' => 'two'];

		$widgetOutput = WidgetTestHelper::runAndCapture($widget);
		$this->assertTag([
			'tag' => $widget->wrapperTag,
			'attributes' => ['one' => 'two']
		], $widgetOutput);
	}

	/**
	 * @covers ::run
	 */
	public function testRun_AlertsRenderingAndSkip()
	{
		$widget = $this->getMock(self::WIDGET_CLASS, ['renderAlert']);
		$widget->alertOptions = [
			'baz' => false,
		];
		$widget->alerts = [
			'baz' => 'blah',
			'foo' => 'bar'
		];
		$widget->expects($this->once())
			->method('renderAlert')
			->with($widget->alertTag, 'foo', 'bar', $this->callback(function ($arg) {
				return is_array($arg) && isset($arg['block']) && isset($arg['fade'])
				&& isset($arg['htmlOptions']) && isset($arg['closeText']);
			}));
		WidgetTestHelper::runAndCapture($widget);
	}

	/**
	 * @covers ::run
	 */
	public function testScriptGeneration()
	{
		$widget = $this->makeWidget();
		$widget->id = 'foo';
		$widget->alerts = ['foo' => 'bar'];
		$widget->events = ['click' => 'return foobar()'];
		WidgetTestHelper::runAndCapture($widget);

		$cs = Bootstrap::instance()->clientScript;
		$script = $cs->scripts[$cs->defaultScriptPosition][get_class($widget) . '#' . $widget->getId()];
		$this->assertEquals(
			"jQuery('#foo .alert').on('click','return foobar()');",
			$script
		);
	}

	public function renderAlertProvider()
	{
		return [
			[
				'div', 'foo', 'bar', [
				'htmlOptions' => [],
				'closeText' => false,
				'block' => false,
				'fade' => false,
			]
			],
			[
				'div', 'foo', 'bar', [
				'htmlOptions' => ['one' => 'two'],
				'closeText' => 'CLOSE_TEXT',
				'block' => true,
				'fade' => true,
			]
			],
		];
	}

	/**
	 * @covers ::renderAlert
	 * @dataProvider renderAlertProvider
	 */
	public function testRenderAlert($tag, $type, $alert, $options)
	{
		$widget = $this->makeWidget();
		$method = new ReflectionMethod($widget, 'renderAlert');
		$method->setAccessible(true);

		ob_start();
		$method->invoke($widget, $tag, $type, $alert, $options);
		$alertOutput = ob_get_clean();
		$this->assertTag(['tag' => $tag, 'attributes' => ['class' => 'regexp:/alert\-' . $type . '/']], $alertOutput);
		if ($options['fade']) {
			$this->assertTag(['tag' => $tag, 'attributes' => ['class' => 'regexp:/fade/']], $alertOutput);
		}
		if ($options['block']) {
			$this->assertTag(['tag' => $tag, 'attributes' => ['class' => 'regexp:/alert\-block/']], $alertOutput);
		}
		if (!empty($options['closeText'])) {
			$this->assertTag(
				['tag' => 'a', 'parent' => ['tag' => $tag], 'attributes' => ['class' => 'close']],
				$alertOutput
			);
		}
	}
}