<?php

use Intersvyaz\Bootstrap\LinkPager;

/**
 * @coversDefaultClass \Intersvyaz\Bootstrap\LinkPager
 */
class LinkPagerTest extends \PHPUnit_Framework_TestCase
{
	const TEST_CLASS = '\Intersvyaz\Bootstrap\LinkPager';

	/**
	 * @return LinkPager
	 */
	protected function makeWidget()
	{
		$className = self::TEST_CLASS;
		$widget = new $className;
		$pagination = new CPagination(500);
		$pagination->route = 'test';
		$widget->setPages($pagination);

		return $widget;
	}

	/**
	 * @covers ::run
	 */
	public function testRun()
	{
		$widget = $this->makeWidget();
		$widget->pages->currentPage = 0;
		$widgetOutput = TestHelper::runAndCapture($widget);
		$this->assertTag([
			'tag' => 'li',
			'parent' => [
				'tag' => 'ul',
			]
		], $widgetOutput);
	}

	/**
	 * @cover ::registerClientScript
	 */
	public function testRegisterClientScript()
	{
		/** @var CClientScript $cs */
		$cs = Yii::app()->clientScript;
		$cs->reset();

		$widget = $this->makeWidget();
		$widget->cssFile = false;
		TestHelper::runAndCapture($widget);
		$this->assertEmpty(TestHelper::getPropValue($cs, 'cssFiles'));

		$widget = $this->makeWidget();
		$widget->cssFile = 'blargh';
		TestHelper::runAndCapture($widget);
		$this->assertTrue($cs->isCssFileRegistered('blargh'));
	}
}
