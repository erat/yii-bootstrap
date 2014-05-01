<?php

namespace Intersvyaz\YayBootstrap;

class DateTimePicker extends \CInputWidget
{
	const ASSET_NAME = 'datetimepicker';

	/**
	 * @var array the options for the bootstrap-datetimepicker plugin.
	 */
	public $options = [];

	/**
	 * @var string[] the JavaScript event handlers.
	 */
	public $events = [];

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		if (!isset($this->options['language'])) {
			$this->options['language'] = \Yii::app()->getLanguage();
		}
	}

	/**
	 * @inheritdoc
	 */
	public function run()
	{
		list($name, $id) = $this->resolveNameID();

		if (isset($this->htmlOptions['id'])) {
			$id = $this->htmlOptions['id'];
		} else {
			$this->htmlOptions['id'] = $id;
		}
		if (isset($this->htmlOptions['name'])) {
			$name = $this->htmlOptions['name'];
		}

		if ($this->hasModel()) {
			echo Html::activeTextField($this->model, $this->attribute, $this->htmlOptions);
		} else {
			echo Html::textField($name, $this->value, $this->htmlOptions);
		}

		$options = !empty($this->options) ? \CJavaScript::encode($this->options) : '';

		$script = "jQuery('#{$id}').datetimepicker({$options})";
		foreach ($this->events as $event => $handler) {
			$script .= ".on('{$event}'," . \CJavaScript::encode($handler) . ")";
		}
		$script .= ';';

		Bootstrap::instance()->clientScript
			->registerScript(__CLASS__ . '#' . $this->getId(), $script);
		$this->registerAssets();
	}

	/**
	 * Register widget assets.
	 */
	protected function registerAssets()
	{
		$assetPath = Bootstrap::instance()->resolveAssetPath(static::ASSET_NAME);
		$assetUrl = Bootstrap::instance()->assetManager->publish($assetPath);
		$minifyPrefix = Bootstrap::instance()->minifyAssets ? '.min' : '';

		Bootstrap::instance()->clientScript
			->registerCssFile($assetUrl . '/bootstrap-datetimepicker' . $minifyPrefix . '.css')
			->registerScriptFile($assetUrl . '/bootstrap-datetimepicker' . $minifyPrefix . '.js');

		$localeFile = '/locales/bootstrap-datetimepicker.' . $this->options['language'] . '.js';
		if (file_exists($assetPath . $localeFile)) {
			Bootstrap::instance()->clientScript->registerScriptFile($assetUrl . $localeFile);
		}
	}
}

