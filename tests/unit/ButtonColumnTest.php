<?php

use \Intersvyaz\Bootstrap\ButtonColumn;

/**
 * @coversDefaultClass \Intersvyaz\Bootstrap\ButtonColumn
 */
class ButtonColumnTest extends \PHPUnit_Framework_TestCase
{
	const WIDGET_CLASS = '\Intersvyaz\Bootstrap\ButtonColumn';

	/**
	 * @return ButtonColumn
	 */
	private function makeWidget()
	{
		$grid = new CGridView();

		return new ButtonColumn($grid);
	}

	protected function setUp()
	{
		Yii::import('zii.widgets.grid.CGridView');
	}

	/**
	 * @coversNothing
	 */
	public function testInstanceAndAttributes()
	{
		$widget = $this->makeWidget();
		$this->assertAttributeNotEmpty('viewButtonIcon', $widget);
		$this->assertAttributeNotEmpty('updateButtonIcon', $widget);
		$this->assertAttributeNotEmpty('deleteButtonIcon', $widget);
		$this->assertAttributeInternalType('bool', 'enableTooltips', $widget);
	}

	/**
	 * @covers ::initDefaultButtons
	 */
	public function testInitDefaultButtons()
	{
		$widget = $this->makeWidget();
		$method = new ReflectionMethod(get_class($widget), 'initDefaultButtons');
		$method->setAccessible(true);

		$widget->viewButtonIcon = false;
		$widget->buttons = ['delete' => ['icon' => 'foo']];
		$method->invoke($widget);

		$this->assertArrayNotHasKey('icon', $widget->buttons['view']);
		$this->assertEquals($widget->updateButtonIcon, $widget->buttons['update']['icon']);
		$this->assertEquals('foo', $widget->buttons['delete']['icon']);
	}

	/**
	 * @covers ::renderButton
	 */
	public function testRenderButton()
	{
		$method = new ReflectionMethod(self::WIDGET_CLASS, 'renderButton');
		$method->setAccessible(true);

		ob_start();
		$widget = $this->makeWidget();
		$widget->enableTooltips = true;
		$method->invokeArgs($widget, [
			'view',
			[
				'icon' => 'foo',
				'imageUrl' => 'http://blah'

			],
			'data',
			'row'
		]);
		$widgetContent = ob_get_clean();
		$this->assertTag([
			'tag' => 'a',
			'attributes' => [
				'data-toggle' => 'tooltip',
			],
			'child' => [
				'tag' => 'i',
				'attributes' => [
					'class' => 'regexp:/icon\-foo/'
				]
			]
		], $widgetContent);
	}
}
