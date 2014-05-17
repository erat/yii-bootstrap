<?php

use Intersvyaz\Bootstrap\GridView;

/**
 * @coversDefaultClass \Intersvyaz\Bootstrap\GridView
 */
class GridViewTest extends \PHPUnit_Framework_TestCase
{
	const TEST_CLASS = '\Intersvyaz\Bootstrap\GridView';

	/**
	 * @return GridView
	 */
	protected function makeWidget()
	{
		$className = self::TEST_CLASS;
		$widget = new $className;

		return $widget;
	}

	/**
	 * @coversNothing
	 */
	public function testDefaultProperties()
	{
		$widget = $this->makeWidget();
		$this->assertFalse($widget->cssFile);
		$this->assertNotEmpty($widget->itemsCssClass);
	}
}
