<?php

/**
 * @coversDefaultClass \Intersvyaz\YayBootstrap\ActiveForm
 */
class ActiveFormTest extends \PHPUnit_Framework_TestCase
{
	const WIDGET_CLASS = '\Intersvyaz\YayBootstrap\ActiveForm';

	public function setUp()
	{
		$_SERVER['REQUEST_URI'] = 'test';
		$controller = new \FakeController('fake');
		\Yii::app()->setController($controller);
	}

	/**
	 * @return \Intersvyaz\YayBootstrap\ActiveForm
	 */
	protected function makeWidget()
	{
		$className = self::WIDGET_CLASS;
		return new $className();
	}

	/**
	 * @coversNothing
	 */
	public function testParentClass()
	{
		$this->assertInstanceOf('CActiveForm', $this->makeWidget());
	}

	/*
	 * @covers ::init
	 */
	public function testInitCallParentInit()
	{
		// parent init call
		$form = $this->makeWidget();
		ob_start();
		$form->init();
		$data = ob_get_clean();
		$this->assertStringStartsWith('<form', $data);
	}

	/**
	 * @coversNothing
	 */
	public function testConstantsAndDefaults()
	{
		$form = $this->makeWidget();

		$this->assertAttributeNotEmpty('type', $form);
		$this->assertAttributeEquals(null, 'inlineErrors', $form);
		$this->assertAttributeNotEmpty('prependCssClass', $form);
		$this->assertAttributeNotEmpty('appendCssClass', $form);
		$this->assertAttributeNotEmpty('addOnCssClass', $form);
		$this->assertAttributeNotEmpty('addOnTag', $form);
		$this->assertAttributeNotEmpty('addOnWrapperTag', $form);
		$this->assertAttributeNotEmpty('hintCssClass', $form);
		$this->assertAttributeNotEmpty('hintTag', $form);
	}

	/**
	 * @covers ::init
	 */
	public function testInitFormClass()
	{
		$form = $this->makeWidget();
		$form->type = 'horizontal';
		ob_start();
		$form->init();
		ob_clean();
		$this->assertEquals($form->htmlOptions['class'], 'form-' . $form->type);
	}

	/**
	 * @covers ::init
	 */
	public function testInitInlineErrorsFlag()
	{
		$form = $this->makeWidget();
		$form->type = 'horizontal';
		ob_start();
		$form->init();
		ob_clean();
		$this->assertAttributeEquals(true, 'inlineErrors', $form);

		$form = $this->makeWidget();
		$form->type = 'vertical';
		ob_start();
		$form->init();
		ob_clean();
		$this->assertAttributeEquals(false, 'inlineErrors', $form);

		$form = $this->makeWidget();
		$form->inlineErrors = 999;
		ob_start();
		$form->init();
		ob_clean();
		$this->assertAttributeEquals(999, 'inlineErrors', $form);
	}

	/**
	 * @covers ::init
	 */
	public function testInitErrorMessageCssClass()
	{
		$form = $this->makeWidget();
		$form->inlineErrors = true;
		ob_start();
		$form->init();
		ob_clean();
		$this->assertAttributeEquals('help-inline error', 'errorMessageCssClass', $form);

		$form = $this->makeWidget();
		$form->inlineErrors = false;
		ob_start();
		$form->init();
		ob_clean();
		$this->assertAttributeEquals('help-block error', 'errorMessageCssClass', $form);

		$form = $this->makeWidget();
		$form->errorMessageCssClass = 'foo bar';
		ob_start();
		$form->init();
		ob_clean();
		$this->assertAttributeEquals('foo bar', 'errorMessageCssClass', $form);
	}

	/**
	 * @covers ::init
	 */
	public function testInitClientOptions()
	{
		$form = $this->makeWidget();
		$form->type = 'horizontal';
		ob_start();
		$form->init();
		ob_clean();
		$this->assertEquals('div.control-group', $form->clientOptions['inputContainer']);

		$form = $this->makeWidget();
		$form->type = 'horizontal';
		$form->clientOptions['inputContainer'] = 'foobar';
		ob_start();
		$form->init();
		ob_clean();
		$this->assertEquals('foobar', $form->clientOptions['inputContainer']);

		$form = $this->makeWidget();
		$form->type = 'vertical';
		ob_start();
		$form->init();
		ob_clean();
		$this->assertArrayNotHasKey('inputContainer', $form->clientOptions);
	}

	/**
	 * @covers ::initRowOptions
	 */
	public function testInitRowOptions()
	{
		$form = $this->makeWidget();
		$form->type = 'inline';
		$method = new ReflectionMethod($form, 'initRowOptions');
		$method->setAccessible(true);

		$options = [];
		$model = new FakeModel();
		$attribute = 'password';

		$method->invokeArgs($form, [&$options, &$model, &$attribute]);
		$this->assertArrayHasKey('labelOptions', $options);
		$this->assertInternalType('array', $options['labelOptions']);
		$this->assertArrayHasKey('errorOptions', $options);
		$this->assertInternalType('array', $options['errorOptions']);
		$this->assertArrayHasKey('prependOptions', $options);
		$this->assertInternalType('array', $options['prependOptions']);
		$this->assertArrayHasKey('prepend', $options);
		$this->assertInternalType('null', $options['prepend']);
		$this->assertArrayHasKey('appendOptions', $options);
		$this->assertInternalType('array', $options['appendOptions']);
		$this->assertArrayHasKey('append', $options);
		$this->assertInternalType('null', $options['append']);
	}

	/**
	 * @covers ::renderAddOnBegin
	 */
	public function testRenderAddOnBegin()
	{
		$form = $this->makeWidget();
		$method = new ReflectionMethod($form, 'renderAddOnBegin');
		$method->setAccessible(true);

		ob_start();
		$method->invokeArgs($form, ['foo', 'bar', ['class' => 'foobar']]);
		$actual = new DOMDocument();
		$actual->loadHTML(ob_get_clean() . "</{$form->addOnWrapperTag}>");
		$addonWrapper = $actual->documentElement->getElementsByTagName($form->addOnWrapperTag)->item(0);
		$this->assertContains($form->prependCssClass, $addonWrapper->attributes->getNamedItem('class')->nodeValue);
		$this->assertContains($form->appendCssClass, $addonWrapper->attributes->getNamedItem('class')->nodeValue);
		$addon = $actual->documentElement->getElementsByTagName($form->addOnTag)->item(0);
		$this->assertEquals('foo', $addon->nodeValue);
		$this->assertContains('add-on', $addon->attributes->getNamedItem('class')->nodeValue);
		$this->assertContains('foobar', $addon->attributes->getNamedItem('class')->nodeValue);

		ob_start();
		$method->invokeArgs($form, ['foobar', '', ['isRaw' => true]]);
		$actual = new DOMDocument();
		$actual->loadHTML(ob_get_clean() . "</{$form->addOnWrapperTag}>");
		$addonWrapper = $actual->documentElement->getElementsByTagName($form->addOnWrapperTag)->item(0);
		$this->assertEquals('foobar', $addonWrapper->nodeValue);
	}

	/**
	 * @covers ::renderAddOnEnd
	 */
	public function testRenderAddOnEnd()
	{
		$form = $this->makeWidget();
		$method = new ReflectionMethod($form, 'renderAddOnEnd');
		$method->setAccessible(true);

		ob_start();
		$method->invokeArgs($form, ['foo', ['class' => 'foobar']]);
		$actual = new DOMDocument();
		$actual->loadHTML("<{$form->addOnWrapperTag}>" . ob_get_clean());
		$addon = $actual->documentElement->getElementsByTagName($form->addOnTag)->item(0);
		$this->assertContains('add-on', $addon->attributes->getNamedItem('class')->nodeValue);
		$this->assertContains('foobar', $addon->attributes->getNamedItem('class')->nodeValue);
		$this->assertEquals('foo', $addon->nodeValue);

		ob_start();
		$method->invokeArgs($form, ['foobar', ['isRaw' => true]]);
		$actual = new DOMDocument();
		$actual->loadHTML("<{$form->addOnWrapperTag}>" . ob_get_clean());
		$addonWrapper = $actual->documentElement->getElementsByTagName($form->addOnWrapperTag)->item(0);
		$this->assertEquals('foobar', $addonWrapper->nodeValue);
	}

	/**
	 * @dataProvider dataProviderStandardRows
	 * @covers ::urlFieldRow
	 * @covers ::emailFieldRow
	 * @covers ::numberFieldRow
	 * @covers ::rangeFieldRow
	 * @covers ::dateFieldRow
	 * @covers ::timeFieldRow
	 * @covers ::telFieldRow
	 * @covers ::textFieldRow
	 * @covers ::searchFieldRow
	 * @covers ::passwordFieldRow
	 * @covers ::textAreaRow
	 * @covers ::fileFieldRow
	 * @covers ::radioButtonRow
	 * @covers ::checkBoxRow
	 * @covers ::dropDownListRow
	 * @covers ::listBoxRow
	 * @covers ::checkBoxListRow
	 * @covers ::radioButtonListRow
	 */
	public function testStandardRows($outerMethod, $innerMethod)
	{
		$model = new FakeModel();
		$attribute = 'foobar';

		$mock = $this->getMock(self::WIDGET_CLASS, [$innerMethod]);
		$mock->expects($this->once())->method($innerMethod);
		$mock->$outerMethod($model, $attribute, []);
	}

	public function dataProviderStandardRows()
	{
		return [
			['urlFieldRow', 'urlField'],
			['emailFieldRow', 'emailField'],
			['numberFieldRow', 'numberField'],
			['rangeFieldRow', 'rangeField'],
			['dateFieldRow', 'dateField'],
			['timeFieldRow', 'timeField'],
			['telFieldRow', 'telField'],
			['textFieldRow', 'textField'],
			['searchFieldRow', 'searchField'],
			['passwordFieldRow', 'passwordField'],
			['textAreaRow', 'textArea'],
			['fileFieldRow', 'fileField'],
			['radioButtonRow', 'radioButton'],
			['checkBoxRow', 'checkBox'],
			['dropDownListRow', 'dropDownList'],
			['listBoxRow', 'listBox'],
			['checkBoxListRow', 'checkBoxList'],
			['radioButtonListRow', 'radioButtonList'],
		];
	}

	/**
	 * @covers ::radioButtonRow
	 */
	public function testRadioButtonRow()
	{
		$model = new FakeModel();
		$form = $this->makeWidget();

		$data = $form->radioButtonRow($model, 'login', ['class' => 'foobar'], ['labelOptions' => ['class' => 'foo']]);
		$doc = new DOMDocument();
		$doc->loadHTML($data);
		$actual = new DOMXPath($doc);
		$matches = $actual->query('//input[@type="hidden"]/following-sibling::label[contains(@class, "radio") and contains(@class, "foo")]/input[@type = "radio" and @class = "foobar"]');
		$this->assertEquals(1, $matches->length);
	}

	/**
	 * @covers ::checkBoxRow
	 */
	public function testCheckBoxRow()
	{
		$model = new FakeModel();
		$form = $this->makeWidget();

		$data = $form->checkBoxRow($model, 'login', ['class' => 'foobar'], ['labelOptions' => ['class' => 'foo']]);
		$doc = new DOMDocument();
		$doc->loadHTML($data);
		$actual = new DOMXPath($doc);
		$matches = $actual->query('//input[@type="hidden"]/following-sibling::label[contains(@class, "checkbox") and contains(@class, "foo")]/input[@type = "checkbox" and @class = "foobar"]');
		$this->assertEquals(1, $matches->length);
	}

	/**
	 * @covers ::customFieldRow
	 */
	public function testCustomFieldRow()
	{
		$mock = $this->getMock(self::WIDGET_CLASS, ['customFieldRowInternal', 'initRowOptions']);
		$mock->expects($this->once())->method('initRowOptions');
		$mock->expects($this->once())->method('customFieldRowInternal');

		$mock->customFieldRow('field', null, null);
	}

	/**
	 * @covers ::widgetRow
	 */
	public function testWidgetRow()
	{
		$mock = $this->getMock(self::WIDGET_CLASS, ['customFieldRowInternal', 'initRowOptions']);
		$mock->expects($this->once())->method('initRowOptions');
		$mock->expects($this->once())->method('customFieldRowInternal');

		$mock->widgetRow('foobar', ['model' => null, 'attribute' => null]);
	}

	/**
	 * @covers ::customFieldRowInternal
	 */
	public function testCustomFieldRowInternal()
	{
		$model = new FakeModel();
		$mock = $this->getMock(self::WIDGET_CLASS, ['horizontalFieldRow', 'verticalFieldRow', 'inlineFieldRow']);

		$mock->type = 'horizontal';
		$mock->expects($this->once())->method('horizontalFieldRow');
		$mock->textFieldRow($model, 'login');

		$mock->type = 'vertical';
		$mock->expects($this->once())->method('verticalFieldRow');
		$mock->textFieldRow($model, 'login');

		$mock->type = 'inline';
		$mock->expects($this->once())->method('inlineFieldRow');
		$mock->textFieldRow($model, 'login');

		$form = $this->makeWidget();
		$form->type = 'foobar';
		$this->setExpectedException('CException');
		$form->textFieldRow($model, 'login');
	}

	/**
	 * @covers ::horizontalFieldRow
	 */
	public function testHorizontalFieldRow()
	{
		$model = new FakeModel();
		$fieldData = 'here_will_be_field';
		$attribute = 'login';
		$model->addError($attribute, 'simple error text');
		$form = $this->makeWidget();
		$method = new ReflectionMethod($form, 'horizontalFieldRow');
		$method->setAccessible(true);

		$rowOptions = [
			'labelOptions' => ['class' => 'foo'],
			'prepend' => 'before',
			'prependOptions' => ['class' => 'bar'],
			'append' => 'after',
			'appendOptions' => ['class' => 'apple'],
			'errorOptions' => ['class' => 'i-am-a-banana'],
			'hint' => 'blah',
			'hintOptions' => ['class' => 'codemonkey'],
			'enableAjaxValidation' => true,
			'enableClientValidation' => true
		];

		ob_start();
		$method->invokeArgs($form, [&$fieldData, &$model, &$attribute, &$rowOptions]);
		$data = ob_get_clean();
		$doc = new DOMDocument();
		$doc->loadHTML($data);
		$actual = new DOMXPath($doc);
		$matches = $actual->query(
			'//div[contains(@class, "control-group") and contains(@class, "' . CHtml::$errorCss . '")]'
			. '/label[contains(@class, "control-label") and contains(@class, "' . $rowOptions['labelOptions']['class'] . '")]'
			. '/following-sibling::div[@class="controls"]'
			. '/div[contains(@class, "input-prepend") and contains(@class, "input-append")  and text()="' . $fieldData . '"]'
			. '/span[contains(@class,"add-on") and contains(@class, "' . $rowOptions['prependOptions']['class'] . '") and text()="' . $rowOptions['prepend'] . '"]'
			. '/following-sibling::span[contains(@class,"add-on") and contains(@class, "' . $rowOptions['appendOptions']['class'] . '") and text()="' . $rowOptions['append'] . '"]'
			. '/following::div[@class="' . $rowOptions['errorOptions']['class'] . '"]'
			. '/following-sibling::p[contains(@class,"' . $rowOptions['hintOptions']['class'] . '") and text()="' . $rowOptions['hint'] . '"]'
		);
		$this->assertEquals(1, $matches->length);
	}

	/**
	 * @covers ::verticalFieldRow
	 */
	public function testVerticalFieldRow()
	{
		$model = new FakeModel();
		$fieldData = 'here_will_be_field';
		$attribute = 'login';
		$model->addError($attribute, 'simple error text');
		$form = $this->makeWidget();
		$method = new ReflectionMethod($form, 'verticalFieldRow');
		$method->setAccessible(true);

		$rowOptions = [
			'labelOptions' => ['class' => 'foo'],
			'prepend' => 'before',
			'prependOptions' => ['class' => 'bar'],
			'append' => 'after',
			'appendOptions' => ['class' => 'apple'],
			'errorOptions' => ['class' => 'i-am-a-banana'],
			'hint' => 'blah',
			'hintOptions' => ['class' => 'codemonkey'],
			'enableAjaxValidation' => true,
			'enableClientValidation' => true
		];

		ob_start();
		$method->invokeArgs($form, [&$fieldData, &$model, &$attribute, &$rowOptions]);
		$data = ob_get_clean();
		$doc = new DOMDocument();
		$doc->loadHTML($data);
		$actual = new DOMXPath($doc);
		$matches = $actual->query(
			'//label[contains(@class, "' . $rowOptions['labelOptions']['class'] . '")]'
			. '/following-sibling::div[contains(@class, "input-prepend") and contains(@class, "input-append")  and text()="' . $fieldData . '"]'
			. '/span[contains(@class,"add-on") and contains(@class, "' . $rowOptions['prependOptions']['class'] . '") and text()="' . $rowOptions['prepend'] . '"]'
			. '/following-sibling::span[contains(@class,"add-on") and contains(@class, "' . $rowOptions['appendOptions']['class'] . '") and text()="' . $rowOptions['append'] . '"]'
			. '/following::div[@class="' . $rowOptions['errorOptions']['class'] . '"]'
			. '/following-sibling::p[contains(@class,"' . $rowOptions['hintOptions']['class'] . '") and text()="' . $rowOptions['hint'] . '"]'
		);
		$this->assertEquals(1, $matches->length);
	}

	/**
	 * @covers ::inlineFieldRow
	 */
	public function testInlineFieldRow()
	{
		$model = new FakeModel();
		$fieldData = 'here_will_be_field';
		$attribute = 'login';
		$model->addError($attribute, 'simple error text');
		$form = $this->makeWidget();
		$method = new ReflectionMethod($form, 'inlineFieldRow');
		$method->setAccessible(true);

		$rowOptions = [];
		$methodInitRowOptions = new ReflectionMethod($form, 'initRowOptions');
		$methodInitRowOptions->setAccessible(true);
		$methodInitRowOptions->invokeArgs($form, [&$rowOptions]);

		$rowOptions['prepend'] = 'before';
		$rowOptions['prependOptions'] = ['class' => 'bar'];
		$rowOptions['append'] = 'after';
		$rowOptions['appendOptions'] = ['class' => 'apple'];

		ob_start();
		$method->invokeArgs($form, [&$fieldData, &$model, &$attribute, &$rowOptions]);
		$data = ob_get_clean();
		$doc = new DOMDocument();
		$doc->loadHTML($data);
		$actual = new DOMXPath($doc);
		$matches = $actual->query(
			'//div[contains(@class, "input-prepend") and contains(@class, "input-append") and text()="' . $fieldData . '"]'
			. '/span[contains(@class,"add-on") and contains(@class, "' . $rowOptions['prependOptions']['class'] . '") and text()="' . $rowOptions['prepend'] . '"]'
			. '/following-sibling::span[contains(@class,"add-on") and contains(@class, "' . $rowOptions['appendOptions']['class'] . '") and text()="' . $rowOptions['append'] . '"]'
		);
		$this->assertEquals(1, $matches->length);
	}
}
