<?php

use \Intersvyaz\YayBootstrap\Breadcrumbs;

/**
 * @coversDefaultClass \Intersvyaz\YayBootstrap\Breadcrumbs
 */
class BreadcrumbsTest extends \PHPUnit_Framework_TestCase
{
	const WIDGET_CLASS = '\Intersvyaz\YayBootstrap\Breadcrumbs';

	/**
	 * @return Breadcrumbs
	 */
	private function makeWidget()
	{
		return new Breadcrumbs();
	}

	/**
	 * @coversNothing
	 */
	public function testInstanceAndAttributes()
	{
		$widget = $this->makeWidget();
		$this->assertInstanceOf(self::WIDGET_CLASS, $widget);
		$this->assertInstanceOf('CBreadcrumbs', $widget);

		$this->assertAttributeNotEmpty('tagName', $widget);
		$this->assertAttributeNotEmpty('htmlOptions', $widget);
		$this->assertAttributeNotEmpty('inactiveLinkTemplate', $widget);
		$this->assertAttributeNotEmpty('separator', $widget);
	}

	/**
	 * @covers ::init
	 */
	public function testInit()
	{
		$widget = $this->makeWidget();
		$separator = $widget->separator;
		$widget->init();

		$this->assertAttributeEquals('<span class="divider">' . $separator . '</span>', 'separator', $widget);
		$defaultHomeLink = CHtml::link(Yii::t('zii', 'Home'), Yii::app()->homeUrl);
		$this->assertAttributeEquals($defaultHomeLink, 'homeLink', $widget);
	}

	/**
	 * @covers ::run
	 */
	public function testHomeLink()
	{
		// default home link
		$widget = $this->makeWidget();
		$widget->homeLink = null;
		$widget->links = ['test'];
		WidgetTestHelper::runAndCapture($widget);

		// no home link
		$widget = $this->makeWidget();
		$widget->homeLink = false;
		$widget->links = ['test'];
		$widgetContent = WidgetTestHelper::runAndCapture($widget);
		$this->assertTag([
			'tag' => $widget->tagName,
			'attributes' => ['class' => 'regexp:/breadcrumb/'],
			'child' => [
				'tag' => 'li',
				'attributes' => ['class' => 'active'],
				'content' => 'test'
			]
		], $widgetContent);

		// home link as plain text
		$widget = $this->makeWidget();
		$widget->homeLink = 'foobar';
		$widget->links = ['test'];
		$widgetContent = WidgetTestHelper::runAndCapture($widget);
		$this->assertTag([
			'tag' => $widget->tagName,
			'attributes' => [
				'class' => 'regexp:/breadcrumb/'
			],
			'child' => [
				'tag'=>'li',
				'attributes' => ['class' => 'active'],
				'content' => 'foobar',
				'child' => [
					'tag' => 'span',
					'attributes' => ['class' => 'divider'],
					'content' => '/'
				]
			]
		], $widgetContent);
	}

	/**
	 * @covers ::run
	 */
	public function testRunLinksRendering()
	{
		// no output produced on empty links
		$widget = $this->makeWidget();
		$content = WidgetTestHelper::runAndCapture($widget);
		$this->assertEmpty($content);

		// separator between links
		$widget = $this->makeWidget();
		$widget->homeLink = 'foobar';
		$widget->links = ['foo' => 'bar', 'end'];
		$widgetContent = WidgetTestHelper::runAndCapture($widget);
		$actualHtml = new DOMDocument();
		$actualHtml->loadHTML($widgetContent);

		$expectedHtml = new DOMDocument();
		$expectedHtml->loadHTML('<ul class="breadcrumb"><li class="active">foobar<span class="divider">/</span></li><li><a href="bar">foo</a><span class="divider">/</span></li><li class="active">end</li></ul>');

		$this->assertEquals($expectedHtml, $actualHtml);
	}
}
