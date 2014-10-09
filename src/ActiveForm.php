<?php

namespace Intersvyaz\Bootstrap;

/**
 * This class is extended version of {@link \CActiveForm}, that allows you fully take advantage of bootstrap forms.
 * Basically form consists of rows with label, field, error info, hint text and other useful stuff.
 * ActiveForm brings together all of these things to quickly build custom forms even with non-standard fields.
 *
 * Each field method has $rowOptions for customizing rendering appearance.
 * <ul>
 * <li>'label' - Custom label text</li>
 * <li>'labelOptions' - HTML options for label tag or passed to {@link \CActiveForm::labelEx} call if 'label' is not set</li>
 * <li>'errorOptions' - HTML options for {@link \CActiveForm::error} call</li>
 * <li>'prepend' - Custom text/HTML-code rendered before field</li>
 * <li>'prependOptions' - HTML options for prepend wrapper tag</li>
 * <li>'append' - Custom text/HTML-code rendered after field</li>
 * <li>'appendOptions' - HTML options for append wrapper tag</li>
 * <li>'hint' - Hint text rendered below the field</li>
 * <li>'hintOptions' - HTML options for hint wrapper tag</li>
 * <li>'enableAjaxValidation' - passed to {@link \CActiveForm::error} call</li>
 * <li>'enableClientValidation' - passed to {@link \CActiveForm::error} call</li>
 * </ul>
 *
 * Here's simple example how to build login form using this class:
 * <pre>
 * <?php $form = $this->beginWidget('\Intersvyaz\YiiBootstrap\ActiveForm', array(
 *     'type' => 'horizontal',
 *     'htmlOptions' => array('class' => 'well'),
 * )); ?>
 *
 * <?php echo $form->errorSummary($model); ?>
 *
 * <?php echo $form->textFieldRow($model, 'username'); ?>
 * <?php echo $form->passwordFieldRow($model, 'password', array(), array(
 *     'hint' => 'Check keyboard layout'
 * )); ?>
 * <?php echo $form->checkBoxRow($model, 'rememberMe'); ?>

 * <div class="form-actions">
 *     <?php echo CHtml::submitButton('Login', array('class'=>'btn')); ?>
 * </div>
 *
 * <?php $this->endWidget(); ?>
 * </pre>
 *
 * Additionally this class provides two additional ways to render custom widget or field or even everything you want
 * with {@link ActiveForm::widgetRow} and {@link ActiveForm::customFieldRow}.
 * Examples are simply clear:
 * <code>
 * $form->widgetRow(
 *     'my.super.cool.widget',
 *     array('model' => $model, 'attribute' => $attribute, 'data' => $mydata),
 *     array('hint' => 'Hint text here!')
 * );
 *
 * // suppose that field is rendered via SomeClass::someMethod($model, $attribute) call.
 * $form->customFieldRow(
 *     array(array('SomeClass', 'someMethod'), array($model, $attribute)),
 *     $mode,
 *     $attribute,
 *     array(...)
 * );
 * </code>
 *
 * @see http://getbootstrap.com/2.3.2/base-css.html#forms
 * @see \CActiveForm
 */
class ActiveForm extends \CActiveForm
{
	// Allowed form types.
	const TYPE_VERTICAL = 'vertical';
	const TYPE_INLINE = 'inline';
	const TYPE_HORIZONTAL = 'horizontal';
	const TYPE_SEARCH = 'search';

	/**
	 * @var string The form type. Allowed types are in `TYPE_*` constants.
	 */
	public $type = self::TYPE_VERTICAL;

	/**
	 * @var bool Whether to render errors inline.
	 */
	public $inlineErrors;

	/**
	 * @var string Prepend wrapper CSS class.
	 */
	public $prependCssClass = 'input-prepend';

	/**
	 * @var string Append wrapper CSS class.
	 */
	public $appendCssClass = 'input-append';

	/**
	 * @var string Add-on CSS class.
	 */
	public $addOnCssClass = 'add-on';

	/**
	 * @var string Add-on wrapper tag.
	 */
	public $addOnTag = 'span';

	/**
	 * @var string Tag for wrapping field with prepended and/or appended data.
	 */
	public $addOnWrapperTag = 'div';

	/**
	 * @var string Hint CSS class.
	 */
	public $hintCssClass = 'help-block';

	/**
	 * @var string Hint wrapper tag.
	 */
	public $hintTag = 'p';

	/**
	 * @var bool Whether to render field error after input. Only for vertical and horizontal types.
	 */
	public $showErrors = true;

	/**
	 * Initializes the widget.
	 * This renders the form open tag.
	 */
	public function init()
	{
		Html::addCssClass($this->htmlOptions, 'form-' . $this->type);

		if (!isset($this->inlineErrors)) {
			$this->inlineErrors = $this->type === self::TYPE_HORIZONTAL;
		}

		if (!isset($this->errorMessageCssClass)) {
			if ($this->inlineErrors) {
				$this->errorMessageCssClass = 'help-inline error';
			} else {
				$this->errorMessageCssClass = 'help-block error';
			}
		}

		if ($this->type == self::TYPE_HORIZONTAL && !isset($this->clientOptions['inputContainer']))
			$this->clientOptions['inputContainer'] = 'div.control-group';

		parent::init();
	}

	/**
	 * Displays a summary of validation errors for one or several models.
	 *
	 * This method is a wrapper for {@link \CActiveForm::errorSummary}.
	 *
	 * @param mixed $models The models whose input errors are to be displayed. This can be either a single model or an array of models.
	 * @param string $header A piece of HTML code that appears in front of the errors
	 * @param string $footer A piece of HTML code that appears at the end of the errors
	 * @param array $htmlOptions Additional HTML attributes to be rendered in the container div tag.
	 * @return string The error summary. Empty if no errors are found.
	 * @see \CActiveForm::errorSummary
	 */
	public function errorSummary($models, $header = null, $footer = null, $htmlOptions = [])
	{
		if (!isset($htmlOptions['class'])) {
			$htmlOptions['class'] = 'alert alert-block alert-error';
		}

		return parent::errorSummary($models, $header, $footer, $htmlOptions);
	}

	/**
	 * Generates a url field row for a model attribute.
	 *
	 * This method is a wrapper for {@link \CActiveForm::urlField} and {@link customFieldRow}.
	 * Please check {@link \CActiveForm::urlField} for detailed information about $htmlOptions argument.
	 * About $rowOptions argument parameters see {@link ActiveForm} documentation.
	 *
	 * @param \CModel $model The data model.
	 * @param string $attribute The attribute.
	 * @param array $htmlOptions Additional HTML attributes.
	 * @param array $rowOptions Row attributes.
	 * @return string The generated url field row.
	 * @see \CActiveForm::urlField
	 * @see customFieldRow
	 */
	public function urlFieldRow($model, $attribute, $htmlOptions = [], $rowOptions = [])
	{
		$this->initRowOptions($rowOptions);

		$fieldData = [[$this, 'urlField'], [$model, $attribute, $htmlOptions]];

		return $this->customFieldRowInternal($fieldData, $model, $attribute, $rowOptions);
	}

	/**
	 * Generates an email field row for a model attribute.
	 *
	 * This method is a wrapper for {@link \CActiveForm::emailField} and {@link customFieldRow}.
	 * Please check {@link \CActiveForm::emailField} for detailed information about $htmlOptions argument.
	 * About $rowOptions argument parameters see {@link ActiveForm} documentation.
	 *
	 * @param \CModel $model The data model.
	 * @param string $attribute The attribute.
	 * @param array $htmlOptions Additional HTML attributes.
	 * @param array $rowOptions Row attributes.
	 * @return string the generated email field row.
	 * @see \CActiveForm::emailField
	 * @see customFieldRow
	 */
	public function emailFieldRow($model, $attribute, $htmlOptions = [], $rowOptions = [])
	{
		$this->initRowOptions($rowOptions);

		$fieldData = [[$this, 'emailField'], [$model, $attribute, $htmlOptions]];

		return $this->customFieldRowInternal($fieldData, $model, $attribute, $rowOptions);
	}

	/**
	 * Generates a number field row for a model attribute.
	 *
	 * This method is a wrapper for {@link \CActiveForm::numberField} and {@link customFieldRow}.
	 * Please check {@link \CActiveForm::numberField} for detailed information about $htmlOptions argument.
	 * About $rowOptions argument parameters see {@link ActiveForm} documentation.
	 *
	 * @param \CModel $model The data model.
	 * @param string $attribute The attribute.
	 * @param array $htmlOptions Additional HTML attributes.
	 * @param array $rowOptions Row attributes.
	 * @return string The generated number filed row.
	 * @see \CActiveForm::numberField
	 * @see customFieldRow
	 */
	public function numberFieldRow($model, $attribute, $htmlOptions = [], $rowOptions = [])
	{
		$this->initRowOptions($rowOptions);

		$fieldData = [[$this, 'numberField'], [$model, $attribute, $htmlOptions]];

		return $this->customFieldRowInternal($fieldData, $model, $attribute, $rowOptions);
	}

	/**
	 * Generates a range field row for a model attribute.
	 *
	 * This method is a wrapper for {@link \CActiveForm::rangeField} and {@link customFieldRow}.
	 * Please check {@link \CActiveForm::rangeField} for detailed information about $htmlOptions argument.
	 * About $rowOptions argument parameters see {@link ActiveForm} documentation.
	 *
	 * @param \CModel $model The data model.
	 * @param string $attribute The attribute.
	 * @param array $htmlOptions Additional HTML attributes.
	 * @param array $rowOptions Row attributes.
	 * @return string The generated range field row.
	 * @see \CActiveForm::rangeField
	 * @see customFieldRow
	 */
	public function rangeFieldRow($model, $attribute, $htmlOptions = [], $rowOptions = [])
	{
		$this->initRowOptions($rowOptions);

		$fieldData = [[$this, 'rangeField'], [$model, $attribute, $htmlOptions]];

		return $this->customFieldRowInternal($fieldData, $model, $attribute, $rowOptions);
	}

	/**
	 * Generates a date field row for a model attribute.
	 *
	 * This method is a wrapper for {@link \CActiveForm::dateField} and {@link customFieldRow}.
	 * Please check {@link \CActiveForm::dateField} for detailed information about $htmlOptions argument.
	 * About $rowOptions argument parameters see {@link ActiveForm} documentation.
	 *
	 * @param \CModel $model The data model.
	 * @param string $attribute The attribute.
	 * @param array $htmlOptions Additional HTML attributes.
	 * @param array $rowOptions Row attributes.
	 * @return string The generated date field row.
	 * @see \CActiveForm::dateField
	 * @see customFieldRow
	 */
	public function dateFieldRow($model, $attribute, $htmlOptions = [], $rowOptions = [])
	{
		$this->initRowOptions($rowOptions);

		$fieldData = [[$this, 'dateField'], [$model, $attribute, $htmlOptions]];

		return $this->customFieldRowInternal($fieldData, $model, $attribute, $rowOptions);
	}

	/**
	 * Generates a time field row for a model attribute.
	 *
	 * This method is a wrapper for {@link \CActiveForm::timeField} and {@link customFieldRow}.
	 * Please check {@link \CActiveForm::timeField} for detailed information about $htmlOptions argument.
	 * About $rowOptions argument parameters see {@link ActiveForm} documentation.
	 *
	 * @param \CModel $model The data model.
	 * @param string $attribute The attribute.
	 * @param array $htmlOptions Additional HTML attributes.
	 * @param array $rowOptions Row attributes.
	 * @return string The generated date field row.
	 * @see \CActiveForm::timeField
	 * @see customFieldRow
	 */
	public function timeFieldRow($model, $attribute, $htmlOptions = [], $rowOptions = [])
	{
		$this->initRowOptions($rowOptions);

		$fieldData = [[$this, 'timeField'], [$model, $attribute, $htmlOptions]];

		return $this->customFieldRowInternal($fieldData, $model, $attribute, $rowOptions);
	}

	/**
	 * Generates a tel field row for a model attribute.
	 *
	 * This method is a wrapper for {@link \CActiveForm::telField} and {@link customFieldRow}.
	 * Please check {@link \CActiveForm::telField} for detailed information about $htmlOptions argument.
	 * About $rowOptions argument parameters see {@link ActiveForm} documentation.
	 *
	 * @param \CModel $model The data model.
	 * @param string $attribute The attribute.
	 * @param array $htmlOptions Additional HTML attributes.
	 * @param array $rowOptions Row attributes.
	 * @return string The generated date field row.
	 * @see \CActiveForm::telField
	 * @see customFieldRow
	 */
	public function telFieldRow($model, $attribute, $htmlOptions = [], $rowOptions = [])
	{
		$this->initRowOptions($rowOptions);

		$fieldData = [[$this, 'telField'], [$model, $attribute, $htmlOptions]];

		return $this->customFieldRowInternal($fieldData, $model, $attribute, $rowOptions);
	}

	/**
	 * Generates a text field row for a model attribute.
	 *
	 * This method is a wrapper for {@link \CActiveForm::textField} and {@link customFieldRow}.
	 * Please check {@link \CActiveForm::textField} for detailed information about $htmlOptions argument.
	 * About $rowOptions argument parameters see {@link ActiveForm} documentation.
	 *
	 * @param \CModel $model The data model.
	 * @param string $attribute The attribute.
	 * @param array $htmlOptions Additional HTML attributes.
	 * @param array $rowOptions Row attributes.
	 * @return string The generated text field row.
	 * @see \CActiveForm::textField
	 * @see customFieldRow
	 */
	public function textFieldRow($model, $attribute, $htmlOptions = [], $rowOptions = [])
	{
		$this->initRowOptions($rowOptions);

		$fieldData = [[$this, 'textField'], [$model, $attribute, $htmlOptions]];

		return $this->customFieldRowInternal($fieldData, $model, $attribute, $rowOptions);
	}

	/**
	 * Generates a search field row for a model attribute.
	 *
	 * This method is a wrapper for {@link \CActiveForm::searchField} and {@link customFieldRow}.
	 * Please check {@link \CActiveForm::searchField} for detailed information about $htmlOptions argument.
	 * About $rowOptions argument parameters see {@link ActiveForm} documentation.
	 *
	 * @param \CModel $model The data model.
	 * @param string $attribute The attribute.
	 * @param array $htmlOptions Additional HTML attributes.
	 * @param array $rowOptions Row attributes.
	 * @return string The generated text field row.
	 * @see CActiveForm::searchField
	 * @see customFieldRow
	 */
	public function searchFieldRow($model, $attribute, $htmlOptions = [], $rowOptions = [])
	{
		$this->initRowOptions($rowOptions);

		if ($this->type == self::TYPE_SEARCH) {
			Html::addCssClass($htmlOptions, 'search-query');
		}

		$fieldData = [[$this, 'searchField'], [$model, $attribute, $htmlOptions]];

		return $this->customFieldRowInternal($fieldData, $model, $attribute, $rowOptions);
	}

	/**
	 * Generates a password field row for a model attribute.
	 *
	 * This method is a wrapper for {@link \CActiveForm::passwordField} and {@link customFieldRow}.
	 * Please check {@link \CActiveForm::passwordField} for detailed information about $htmlOptions argument.
	 * About $rowOptions argument parameters see {@link ActiveForm} documentation.
	 *
	 * @param \CModel $model The data model.
	 * @param string $attribute The attribute.
	 * @param array $htmlOptions Additional HTML attributes.
	 * @param array $rowOptions Row attributes.
	 * @return string The generated password field row.
	 * @see \CActiveForm::passwordField
	 * @see customFieldRow
	 */
	public function passwordFieldRow($model, $attribute, $htmlOptions = [], $rowOptions = [])
	{
		$this->initRowOptions($rowOptions);

		$fieldData = [[$this, 'passwordField'], [$model, $attribute, $htmlOptions]];

		return $this->customFieldRowInternal($fieldData, $model, $attribute, $rowOptions);
	}

	/**
	 * Generates a text area row for a model attribute.
	 *
	 * This method is a wrapper for {@link \CActiveForm::textArea} and {@link customFieldRow}.
	 * Please check {@link \CActiveForm::textArea} for detailed information about $htmlOptions argument.
	 * About $rowOptions argument parameters see {@link ActiveForm} documentation.
	 *
	 * @param \CModel $model The data model.
	 * @param string $attribute The attribute.
	 * @param array $htmlOptions Additional HTML attributes.
	 * @param array $rowOptions Row attributes.
	 * @return string The generated text area row.
	 * @see \CActiveForm::textArea
	 * @see customFieldRow
	 */
	public function textAreaRow($model, $attribute, $htmlOptions = [], $rowOptions = [])
	{
		$this->initRowOptions($rowOptions);

		$fieldData = [[$this, 'textArea'], [$model, $attribute, $htmlOptions]];

		return $this->customFieldRowInternal($fieldData, $model, $attribute, $rowOptions);
	}

	/**
	 * Generates a file field row for a model attribute.
	 *
	 * This method is a wrapper for {@link \CActiveForm::fileField} and {@link customFieldRow}.
	 * Please check {@link \CActiveForm::fileField} for detailed information about $htmlOptions argument.
	 * About $rowOptions argument parameters see {@link ActiveForm} documentation.
	 *
	 * @param \CModel $model The data model.
	 * @param string $attribute The attribute.
	 * @param array $htmlOptions Additional HTML attributes.
	 * @param array $rowOptions Row attributes.
	 * @return string The generated file field row.
	 * @see \CActiveForm::fileField
	 * @see customFieldRow
	 */
	public function fileFieldRow($model, $attribute, $htmlOptions = [], $rowOptions = [])
	{
		$this->initRowOptions($rowOptions);

		$fieldData = [[$this, 'fileField'], [$model, $attribute, $htmlOptions]];

		return $this->customFieldRowInternal($fieldData, $model, $attribute, $rowOptions);
	}

	/**
	 * Generates a radio button row for a model attribute.
	 *
	 * This method is a wrapper for {@link \CActiveForm::radioButton} and {@link customFieldRow}.
	 * Please check {@link \CActiveForm::radioButton} for detailed information about $htmlOptions argument.
	 * About $rowOptions argument parameters see {@link ActiveForm} documentation.
	 *
	 * @param \CModel $model The data model.
	 * @param string $attribute The attribute.
	 * @param array $htmlOptions Additional HTML attributes.
	 * @param array $rowOptions Row attributes.
	 * @return string The generated radio button row.
	 * @see \CActiveForm::radioButton
	 * @see customFieldRow
	 */
	public function radioButtonRow($model, $attribute, $htmlOptions = [], $rowOptions = [])
	{
		$this->initRowOptions($rowOptions);

		Html::addCssClass($rowOptions['labelOptions'], 'radio');
		if ($this->type == self::TYPE_INLINE)
			Html::addCssClass($rowOptions['labelOptions'], 'inline');

		$field = $this->radioButton($model, $attribute, $htmlOptions);
		if ((!array_key_exists('uncheckValue', $htmlOptions) || isset($htmlOptions['uncheckValue']))
			&& preg_match('/\<input.*?type="hidden".*?\>/', $field, $matches)
		) {
			$hiddenField = $matches[0];
			$field = str_replace($hiddenField, '', $field);
		}

		$realAttribute = $attribute;
		\CHtml::resolveName($model, $realAttribute);

		ob_start();
		if (isset($hiddenField)) echo $hiddenField;
		echo \CHtml::tag('label', $rowOptions['labelOptions'], false, false);
		echo $field;
		if (isset($rowOptions['label'])) {
			if ($rowOptions['label']) echo $rowOptions['label'];
		} else {
			echo $model->getAttributeLabel($realAttribute);
		}
		echo \CHtml::closeTag('label');
		$fieldData = ob_get_clean();

		$rowOptions['label'] = '';

		return $this->customFieldRowInternal($fieldData, $model, $attribute, $rowOptions);
	}

	/**
	 * Generates a checkbox row for a model attribute.
	 *
	 * This method is a wrapper for {@link \CActiveForm::checkBox} and {@link customFieldRow}.
	 * Please check {@link \CActiveForm::checkBox} for detailed information about $htmlOptions argument.
	 * About $rowOptions argument parameters see {@link ActiveForm} documentation.
	 *
	 * @param \CModel $model The data model.
	 * @param string $attribute The attribute.
	 * @param array $htmlOptions Additional HTML attributes.
	 * @param array $rowOptions Row attributes.
	 * @return string The generated checkbox row.
	 * @see \CActiveForm::checkBox
	 * @see customFieldRow
	 */
	public function checkBoxRow($model, $attribute, $htmlOptions = [], $rowOptions = [])
	{
		$this->initRowOptions($rowOptions);

		Html::addCssClass($rowOptions['labelOptions'], 'checkbox');
		if ($this->type == self::TYPE_INLINE)
			Html::addCssClass($rowOptions['labelOptions'], 'inline');

		$field = $this->checkBox($model, $attribute, $htmlOptions);
		if ((!array_key_exists('uncheckValue', $htmlOptions) || isset($htmlOptions['uncheckValue']))
			&& preg_match('/\<input.*?type="hidden".*?\>/', $field, $matches)
		) {
			$hiddenField = $matches[0];
			$field = str_replace($hiddenField, '', $field);
		}

		$realAttribute = $attribute;
		\CHtml::resolveName($model, $realAttribute);

		ob_start();
		if (isset($hiddenField)) echo $hiddenField;
		echo \CHtml::tag('label', $rowOptions['labelOptions'], false, false);
		echo $field;
		if (isset($rowOptions['label'])) {
			if ($rowOptions['label']) echo $rowOptions['label'];
		} else {
			echo $model->getAttributeLabel($realAttribute);
		}
		echo \CHtml::closeTag('label');
		$fieldData = ob_get_clean();

		$rowOptions['label'] = '';

		return $this->customFieldRowInternal($fieldData, $model, $attribute, $rowOptions);
	}

	/**
	 * Generates a dropdown list row for a model attribute.
	 *
	 * This method is a wrapper for {@link \CActiveForm::dropDownList} and {@link customFieldRow}.
	 * Please check {@link \CActiveForm::dropDownList} for detailed information about $htmlOptions argument.
	 * About $rowOptions argument parameters see {@link ActiveForm} documentation.
	 *
	 * @param \CModel $model The data model.
	 * @param string $attribute The attribute.
	 * @param array $data Data for generating the list options (value=>display).
	 * @param array $htmlOptions Additional HTML attributes.
	 * @param array $rowOptions Row attributes.
	 * @return string The generated drop down list row.
	 * @see \CActiveForm::dropDownList
	 * @see customFieldRow
	 */
	public function dropDownListRow($model, $attribute, $data, $htmlOptions = [], $rowOptions = [])
	{
		$this->initRowOptions($rowOptions);

		$fieldData = [[$this, 'dropDownList'], [$model, $attribute, $data, $htmlOptions]];

		return $this->customFieldRowInternal($fieldData, $model, $attribute, $rowOptions);
	}

	/**
	 * Generates a list box row for a model attribute.
	 *
	 * This method is a wrapper for {@link \CActiveForm::listBox} and {@link customFieldRow}.
	 * Please check {@link \CActiveForm::listBox} for detailed information about $htmlOptions argument.
	 * About $rowOptions argument parameters see {@link ActiveForm} documentation.
	 *
	 * @param \CModel $model The data model.
	 * @param string $attribute The attribute.
	 * @param array $data
	 * @param array $htmlOptions Additional HTML attributes.
	 * @param array $rowOptions Row attributes.
	 * @return string The generated list box row.
	 * @see \CActiveForm::listBox
	 * @see customFieldRow
	 */
	public function listBoxRow($model, $attribute, $data, $htmlOptions = [], $rowOptions = [])
	{
		$this->initRowOptions($rowOptions);

		$fieldData = [[$this, 'listBox'], [$model, $attribute, $data, $htmlOptions]];

		return $this->customFieldRowInternal($fieldData, $model, $attribute, $rowOptions);
	}

	/**
	 * Generates a checkbox list row for a model attribute.
	 *
	 * This method is a wrapper for {@link \CActiveForm::checkBoxList} and {@link customFieldRow}.
	 * Please check {@link \CActiveForm::checkBoxList} for detailed information about $htmlOptions argument.
	 * About $rowOptions argument parameters see {@link ActiveForm} documentation.
	 *
	 * @param \CModel $model The data model.
	 * @param string $attribute The attribute.
	 * @param array $data Value-label pairs used to generate the check box list.
	 * @param array $htmlOptions Additional HTML attributes.
	 * @param array $rowOptions Row attributes.
	 * @return string The generated checkbox list row.
	 * @see \CActiveForm::checkBoxList
	 * @see customFieldRow
	 */
	public function checkBoxListRow($model, $attribute, $data, $htmlOptions = [], $rowOptions = [])
	{
		$this->initRowOptions($rowOptions);

		if (!isset($htmlOptions['labelOptions']['class']))
			$htmlOptions['labelOptions']['class'] = 'checkbox';

		if (!isset($htmlOptions['template']))
			$htmlOptions['template'] = '{beginLabel}{input}{labelTitle}{endLabel}';

		if (!isset($htmlOptions['separator']))
			$htmlOptions['separator'] = "\n";

		$fieldData = [[$this, 'checkBoxList'], [$model, $attribute, $data, $htmlOptions]];

		return $this->customFieldRowInternal($fieldData, $model, $attribute, $rowOptions);
	}

	/**
	 * Generates a radio button list row for a model attribute.
	 *
	 * This method is a wrapper for {@link \CActiveForm::radioButtonList} and {@link customFieldRow}.
	 * Please check {@link CActiveForm::radioButtonList} for detailed information about $htmlOptions argument.
	 * About $rowOptions argument parameters see {@link ActiveForm} documentation.
	 *
	 * @param \CModel $model The data model.
	 * @param string $attribute The attribute.
	 * @param array $data Value-label pairs used to generate the radio button list.
	 * @param array $htmlOptions Additional HTML attributes.
	 * @param array $rowOptions Row attributes.
	 * @return string The generated radio button list row.
	 * @see \CActiveForm::radioButtonList
	 * @see customFieldRow
	 */
	public function radioButtonListRow($model, $attribute, $data, $htmlOptions = [], $rowOptions = [])
	{
		$this->initRowOptions($rowOptions);

		if (!isset($htmlOptions['labelOptions']['class']))
			$htmlOptions['labelOptions']['class'] = 'radio';

		if (!isset($htmlOptions['template']))
			$htmlOptions['template'] = '{beginLabel}{input}{labelTitle}{endLabel}';

		if (!isset($htmlOptions['separator']))
			$htmlOptions['separator'] = "\n";

		$fieldData = [[$this, 'radioButtonList'], [$model, $attribute, $data, $htmlOptions]];

		return $this->customFieldRowInternal($fieldData, $model, $attribute, $rowOptions);
	}

	/**
	 * Generates a custom field row for a model attribute.
	 * About $rowOptions argument parameters see {@link ActiveForm} documentation.
	 *
	 * @param array|string $fieldData Pre-rendered field as string or array of arguments for call_user_func_array()
	 * function.
	 * @param \CModel $model The data model.
	 * @param string $attribute The attribute.
	 * @param array $rowOptions Row attributes.
	 * @return string The generated custom filed row.
	 * @see call_user_func_array
	 */
	public function customFieldRow($fieldData, $model, $attribute, $rowOptions = [])
	{
		$this->initRowOptions($rowOptions);

		return $this->customFieldRowInternal($fieldData, $model, $attribute, $rowOptions);
	}

	/**
	 * Generates a widget row for a model attribute.
	 *
	 * This method is a wrapper for {@link \CBaseController::widget} and {@link customFieldRow}.
	 * Read detailed information about $widgetOptions in $properties argument of {@link \CBaseController::widget} method.
	 * About $rowOptions argument parameters see {@link ActiveForm} documentation.
	 * This method relies that widget have $model and $attribute properties.
	 *
	 * @param string $className The widget class name or class in dot syntax (e.g. application.widgets.MyWidget).
	 * @param array $widgetOptions List of initial property values for the widget (Property Name => Property Value).
	 * @param array $rowOptions Row attributes.
	 * @return string The generated widget row.
	 * @see \CBaseController::widget
	 * @see customFieldRow
	 */
	public function widgetRow($className, $widgetOptions = [], $rowOptions = [])
	{
		$this->initRowOptions($rowOptions);

		$fieldData = [[$this->owner, 'widget'], [$className, $widgetOptions, true]];

		return $this->customFieldRowInternal($fieldData, $widgetOptions['model'], $widgetOptions['attribute'], $rowOptions);
	}

	/**
	 * Generates a custom field row for a model attribute.
	 *
	 * It's base function for generating row with field.
	 *
	 * @param array|string $fieldData Pre-rendered field as string or array of arguments for call_user_func_array() function.
	 * @param \CModel $model The data model.
	 * @param string $attribute The attribute.
	 * @param array $rowOptions Row attributes.
	 * @return string The generated custom filed row.
	 * @throws \RuntimeException On invalid form type.
	 */
	protected function customFieldRowInternal(&$fieldData, &$model, &$attribute, &$rowOptions)
	{
		ob_start();

		try {
			switch ($this->type) {
				case self::TYPE_HORIZONTAL:
					$this->horizontalFieldRow($fieldData, $model, $attribute, $rowOptions);
					break;

				case self::TYPE_VERTICAL:
					$this->verticalFieldRow($fieldData, $model, $attribute, $rowOptions);
					break;

				case self::TYPE_INLINE:
				case self::TYPE_SEARCH:
					$this->inlineFieldRow($fieldData, $model, $attribute, $rowOptions);
					break;

				default:
					throw new \RuntimeException('Invalid form type');
			}
		} catch(\Exception $e) {
			ob_end_clean();
			throw $e;
		}

		return ob_get_clean();
	}

	/**
	 * Renders a horizontal custom field row for a model attribute.
	 *
	 * @param array|string $fieldData Pre-rendered field as string or array of arguments for call_user_func_array() function.
	 * @param \CModel $model The data model.
	 * @param string $attribute The attribute.
	 * @param array $rowOptions Row options.
	 */
	protected function horizontalFieldRow(&$fieldData, &$model, &$attribute, &$rowOptions)
	{
		$controlGroupHtmlOptions = ['class' => 'control-group'];
		if ($model->hasErrors($attribute)) {
			Html::addCssClass($controlGroupHtmlOptions, \CHtml::$errorCss);
		}
		echo \CHtml::openTag('div', $controlGroupHtmlOptions);

		Html::addCssClass($rowOptions['labelOptions'], 'control-label');
		if (isset($rowOptions['label'])) {
			if (!empty($rowOptions['label'])) {
				$for = isset($rowOptions['labelOptions']['for'])
					? $rowOptions['labelOptions']['for']
					: \CHtml::activeId($model, $attribute);
				echo \CHtml::label($rowOptions['label'], $for, $rowOptions['labelOptions']);
			}
		} else {
			echo $this->labelEx($model, $attribute, $rowOptions['labelOptions']);
		}

		echo '<div class="controls">';

		if (!empty($rowOptions['prepend']) || !empty($rowOptions['append'])) {
			$this->renderAddOnBegin($rowOptions['prepend'], $rowOptions['append'], $rowOptions['prependOptions']);
		}

		if (is_array($fieldData)) {
			echo call_user_func_array($fieldData[0], $fieldData[1]);
		} else {
			echo $fieldData;
		}

		if (!empty($rowOptions['prepend']) || !empty($rowOptions['append'])) {
			$this->renderAddOnEnd($rowOptions['append'], $rowOptions['appendOptions']);
		}

		if ($this->showErrors && $rowOptions['errorOptions'] !== false) {
			echo $this->error($model, $attribute, $rowOptions['errorOptions'], $rowOptions['enableAjaxValidation'], $rowOptions['enableClientValidation']);
		}

		if (isset($rowOptions['hint'])) {
			if (!isset($rowOptions['hintOptions']['class'])) {
				Html::addCssClass($rowOptions['hintOptions'], $this->hintCssClass);
			}
			echo \CHtml::tag($this->hintTag, $rowOptions['hintOptions'], $rowOptions['hint']);
		}

		echo '</div></div>'; // controls, control-group
	}

	/**
	 * Renders a vertical custom field row for a model attribute.
	 *
	 * @param array|string $fieldData Pre-rendered field as string or array of arguments for call_user_func_array() function.
	 * @param \CModel $model The data model.
	 * @param string $attribute The attribute.
	 * @param array $rowOptions Row options.
	 */
	protected function verticalFieldRow(&$fieldData, &$model, &$attribute, &$rowOptions)
	{
		if (isset($rowOptions['label'])) {
			if (!empty($rowOptions['label'])) {
				$for = isset($rowOptions['labelOptions']['for'])
					? $rowOptions['labelOptions']['for']
					: \CHtml::activeId($model, $attribute);
				echo \CHtml::label($rowOptions['label'], $for, $rowOptions['labelOptions']);
			}
		} else {
			echo $this->labelEx($model, $attribute, $rowOptions['labelOptions']);
		}

		if (!empty($rowOptions['prepend']) || !empty($rowOptions['append'])) {
			$this->renderAddOnBegin($rowOptions['prepend'], $rowOptions['append'], $rowOptions['prependOptions']);
		}

		if (is_array($fieldData)) {
			echo call_user_func_array($fieldData[0], $fieldData[1]);
		} else {
			echo $fieldData;
		}

		if (!empty($rowOptions['prepend']) || !empty($rowOptions['append'])) {
			$this->renderAddOnEnd($rowOptions['append'], $rowOptions['appendOptions']);
		}

		if ($this->showErrors && $rowOptions['errorOptions'] !== false) {
			echo $this->error($model, $attribute, $rowOptions['errorOptions'], $rowOptions['enableAjaxValidation'], $rowOptions['enableClientValidation']);
		}

		if (isset($rowOptions['hint'])) {
			if (!isset($rowOptions['hintOptions']['class'])) {
				Html::addCssClass($rowOptions['hintOptions'], $this->hintCssClass);
			}
			echo \CHtml::tag($this->hintTag, $rowOptions['hintOptions'], $rowOptions['hint']);
		}
	}

	/**
	 * Renders a inline custom field row for a model attribute.
	 *
	 * @param array|string $fieldData Pre-rendered field as string or array of arguments for call_user_func_array() function.
	 * @param \CModel $model The data model.
	 * @param string $attribute The attribute.
	 * @param array $rowOptions Row options.
	 */
	protected function inlineFieldRow(&$fieldData, &$model, &$attribute, &$rowOptions)
	{
		echo '<div class="controls-inline">';

		if (!empty($rowOptions['prepend']) || !empty($rowOptions['append']))
			$this->renderAddOnBegin($rowOptions['prepend'], $rowOptions['append'], $rowOptions['prependOptions']);

		if (is_array($fieldData)) {
			echo call_user_func_array($fieldData[0], $fieldData[1]);
		} else {
			echo $fieldData;
		}

		if (!empty($rowOptions['prepend']) || !empty($rowOptions['append']))
			$this->renderAddOnEnd($rowOptions['append'], $rowOptions['appendOptions']);

		if ($this->showErrors && $rowOptions['errorOptions'] !== false) {
			echo $this->error($model, $attribute, $rowOptions['errorOptions'], $rowOptions['enableAjaxValidation'], $rowOptions['enableClientValidation']);
		}

		echo '</div>';
	}

	/**
	 * Renders add-on begin.
	 *
	 * @param string $prependText Prepended text.
	 * @param string $appendText Appended text.
	 * @param array $prependOptions Prepend options.
	 */
	protected function renderAddOnBegin($prependText, $appendText, $prependOptions)
	{
		$wrapperCssClass = [];
		if (!empty($prependText))
			$wrapperCssClass[] = $this->prependCssClass;
		if (!empty($appendText))
			$wrapperCssClass[] = $this->appendCssClass;

		echo \CHtml::tag($this->addOnWrapperTag, ['class' => implode(' ', $wrapperCssClass)], false, false);
		if (!empty($prependText)) {
			if (isset($prependOptions['isRaw']) && $prependOptions['isRaw']) {
				echo $prependText;
			} else {
				Html::addCssClass($prependOptions, $this->addOnCssClass);
				echo \CHtml::tag($this->addOnTag, $prependOptions, $prependText);
			}
		}
	}

	/**
	 * Renders add-on end.
	 *
	 * @param string $appendText Appended text.
	 * @param array $appendOptions Append options.
	 */
	protected function renderAddOnEnd($appendText, $appendOptions)
	{
		if (!empty($appendText)) {
			if (isset($appendOptions['isRaw']) && $appendOptions['isRaw']) {
				echo $appendText;
			} else {
				Html::addCssClass($appendOptions, $this->addOnCssClass);
				echo \CHtml::tag($this->addOnTag, $appendOptions, $appendText);
			}
		}

		echo \CHtml::closeTag($this->addOnWrapperTag);
	}

	/**
	 * @param array $options Row options.
	 */
	protected function initRowOptions(&$options)
	{
		if (!isset($options['labelOptions']))
			$options['labelOptions'] = [];

		if (!isset($options['errorOptions']))
			$options['errorOptions'] = [];

		if (!isset($options['prependOptions']))
			$options['prependOptions'] = [];

		if (!isset($options['prepend']))
			$options['prepend'] = null;

		if (!isset($options['appendOptions']))
			$options['appendOptions'] = [];

		if (!isset($options['append']))
			$options['append'] = null;

		if(!isset($options['enableAjaxValidation']))
			$options['enableAjaxValidation'] = true;

		if(!isset($options['enableClientValidation']))
			$options['enableClientValidation'] = true;
	}
}
