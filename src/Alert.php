<?php

namespace Intersvyaz\Bootstrap;

/**
 * Bootstrap alert widget.
 * @see http://getbootstrap.com/2.3.2/components.html#alerts
 */
class Alert extends \CWidget
{
	// alert types.
	const TYPE_SUCCESS = 'success';
	const TYPE_INFO = 'info';
	const TYPE_WARNING = 'warning';
	const TYPE_ERROR = 'error';
	const TYPE_DANGER = 'danger'; // same as error

	/**
	 * The configuration options for individual types of alerts ($alertType => [...]).
	 * Each options have same behavior as global. Available options:
	 * <ul>
	 * <li>'htmlOptions'</li>
	 * <li>'closeText' - overwrite closeText</li>
	 * <li>'block' - overwrite block option</li>
	 * <li>'fade' - overwrite fade option</li>
	 * </ul>
	 * @var array
	 */
	public $alertOptions = [];

	/**
	 * Alert html tag.
	 * @var string
	 */
	public $alertTag = 'div';

	/**
	 * Alerts for rendering ($type => $text).
	 * @var array
	 */
	public $alerts = [];

	/**
	 * Close text. Default is to render a diagonal cross symbol. If set to false, no close text will be rendered.
	 * @var string|bool
	 */
	public $closeText = '&times;';

	/**
	 * @var boolean When set, alert has a larger block size.
	 */
	public $block = false;

	/**
	 * Fade out using transitions when alert closed.
	 * @var bool
	 */
	public $fade = true;

	/**
	 * The JavaScript event handlers attached to all alert elements being rendered ($event => $handler).
	 * @var array
	 */
	public $events = [];

	/**
	 * Wrapper tag name.
	 * @var string
	 */
	public $wrapperTag = 'div';

	/**
	 * Wrapper html options.
	 * @var array
	 */
	public $htmlOptions = [];

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		if (!isset($this->htmlOptions['id'])) {
			$this->htmlOptions['id'] = $this->getId();
		}
	}

	/**
	 * @inheritdoc
	 */
	public function run()
	{
		if (empty($this->alerts))
			return;

		$id = $this->htmlOptions['id'];

		echo Html::openTag($this->wrapperTag, $this->htmlOptions);

		foreach ($this->alerts as $type => $alert) {
			$options = isset($this->alertOptions[$type]) ? $this->alertOptions[$type] : [];
			if ($options === false)
				continue;

			if (!isset($options['htmlOptions'])) $options['htmlOptions'] = [];
			if (!isset($options['block'])) $options['block'] = $this->block;
			if (!isset($options['fade'])) $options['fade'] = $this->block;
			if (!isset($options['closeText'])) $options['closeText'] = $this->closeText;

			$this->renderAlert($this->alertTag, $type, $alert, $options);
		}

		echo Html::closeTag($this->wrapperTag);

		if (!empty($this->events)) {
			$script = "jQuery('#{$id} .alert')";
			foreach ($this->events as $name => $handler) {
				$handler = \CJavaScript::encode($handler);
				$script .= ".on('{$name}',{$handler})";
			}
			$script .= ';';
			\Yii::app()->clientScript->registerScript(__CLASS__ . '#' . $id, $script);
		}
	}

	/**
	 * Render single alert.
	 * @param string $tag Alert html tag.
	 * @param string $type Alert type.
	 * @param string $alert Alert text.
	 * @param array $options Alert type specific options.
	 */
	protected function renderAlert($tag, $type, $alert, array $options)
	{
		$classes = ['alert', 'in', 'alert-' . $type];

		if ($options['block']) $classes[] = 'alert-block';
		if ($options['fade']) $classes[] = 'fade';

		Html::addCssClass($options['htmlOptions'], implode(' ', $classes));

		echo Html::openTag($tag, $options['htmlOptions']);

		if ($options['closeText'] !== false) {
			echo '<a href="#" class="close" data-dismiss="alert">' . $options['closeText'] . '</a>';
		}

		echo $alert;
		echo Html::closeTag($tag);
	}
}
