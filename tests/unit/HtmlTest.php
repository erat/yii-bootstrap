<?php

use Intersvyaz\YayBootstrap\Html;

/**
 * @coversDefaultClass \Intersvyaz\YayBootstrap\Html
 */
class HtmlTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @covers ::icon
	 */
	public function testIcon()
	{
		$data = Html::icon('foobar', ['foo' => 'bar']);
		$doc = new DOMDocument();
		$doc->loadHTML($data);
		$actual = new DOMXPath($doc);
		$matches = $actual->query('//i[@class="icon-foobar" and @foo="bar"]');
		$this->assertEquals(1, $matches->length);

		$data = Html::icon('foobar', ['foo' => 'bar'], 'div');
		$doc = new DOMDocument();
		$doc->loadHTML($data);
		$actual = new DOMXPath($doc);
		$matches = $actual->query('//div[@class="icon-foobar" and @foo="bar"]');
		$this->assertEquals(1, $matches->length);
	}

	/**
	 * @covers ::faIcon
	 */
	public function testFaIcon()
	{
		$data = Html::faIcon('foobar', ['foo' => 'bar']);
		$doc = new DOMDocument();
		$doc->loadHTML($data);
		$actual = new DOMXPath($doc);
		$matches = $actual->query('//i[@class="fa fa-foobar" and @foo="bar"]');
		$this->assertEquals(1, $matches->length);

		$data = Html::faIcon('foobar', ['foo' => 'bar'], 'div');
		$doc = new DOMDocument();
		$doc->loadHTML($data);
		$actual = new DOMXPath($doc);
		$matches = $actual->query('//div[@class="fa fa-foobar" and @foo="bar"]');
		$this->assertEquals(1, $matches->length);
	}

	/**
	 * @covers ::addCssClass
	 */
	public function testAddCssClass()
	{
		// do nothing on empty class
		$htmlOptions = [];
		Html::addCssClass($htmlOptions, '');
		$this->assertArrayNotHasKey('class', $htmlOptions);

		// new class in options
		$htmlOptions = [];
		Html::addCssClass($htmlOptions, 'foobar');
		$this->assertArrayHasKey('class', $htmlOptions);
		$this->assertEquals('foobar', $htmlOptions['class']);

		// class already exists and text prepended
		$htmlOptions = ['class' => 'foo'];
		Html::addCssClass($htmlOptions, 'bar');
		$this->assertArrayHasKey('class', $htmlOptions);
		$this->assertEquals('foo bar', $htmlOptions['class']);
	}
}
