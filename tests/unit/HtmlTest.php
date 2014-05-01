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
		$output = Html::icon('foobar', ['foo' => 'bar']);
		$this->assertTag([
			'tag' => 'i',
			'attributes' => [
				'class' => 'icon-foobar',
				'foo' => 'bar'
			]
		], $output);

		$output = Html::icon('foobar', ['foo' => 'bar'], 'div');
		$this->assertTag([
			'tag' => 'div',
			'attributes' => [
				'class' => 'icon-foobar',
				'foo' => 'bar'
			]
		], $output);
	}

	/**
	 * @covers ::faIcon
	 */
	public function testFaIcon()
	{
		$output = Html::faIcon('foobar', ['foo' => 'bar']);
		$this->assertTag([
			'tag' => 'i',
			'attributes' => [
				'class' => 'regexp:/[fa[\s$]|fa-foobar[\s$]]{2}/',
				'foo' => 'bar'
			]
		], $output);

		$output = Html::faIcon('foobar', ['foo' => 'bar'], 'div');
		$this->assertTag([
			'tag' => 'div',
			'attributes' => [
				'class' => 'regexp:/[fa[\s$]|fa-foobar[\s$]]{2}/',
				'foo' => 'bar'
			]
		], $output);
	}

	/**
	 * @covers ::progressBar
	 */
	public function testProgressBar()
	{
		$output = Html::progressBar([]);
		$this->assertEmpty($output);

		$output = Html::progressBar([
			['percent' => 50],
			['percent' => 30, 'type' => 'alert'],
			['percent' => 20, 'type' => 'error', 'content' => 'blah'],
		], ['foo' => 'bar']);
		$this->assertTag([
			'tag' => 'div',
			'parent' => ['tag' => 'div', 'attributes' => ['foo' => 'bar']],
			'attributes' => [
				'class' => 'bar',
				'style' => 'regexp:/width:50%/'
			]
		], $output);
		$this->assertTag([
			'tag' => 'div',
			'parent' => ['tag' => 'div', 'attributes' => ['foo' => 'bar']],
			'attributes' => [
				'class' => 'bar bar-alert',
				'style' => 'regexp:/width:30%/'
			]
		], $output);
		$this->assertTag([
			'tag' => 'div',
			'parent' => ['tag' => 'div', 'attributes' => ['foo' => 'bar']],
			'content' => 'blah',
			'attributes' => [
				'class' => 'bar bar-error',
				'style' => 'regexp:/width:20%/',
			]
		], $output);
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
