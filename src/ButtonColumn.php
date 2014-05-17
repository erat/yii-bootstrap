<?php

namespace Intersvyaz\Bootstrap;

/**
 * Bootstrap styled Yii's {@link \CButtonColumn}.
 */
class ButtonColumn extends \CButtonColumn
{
	/**
	 * The view button icon.
	 * @var string
	 */
	public $viewButtonIcon = 'eye-open';

	/**
	 * The update button icon.
	 * @var string
	 */
	public $updateButtonIcon = 'pencil';

	/**
	 * The delete button icon.
	 * @var string
	 */
	public $deleteButtonIcon = 'trash';

	/**
	 * Whether to enable bootstrap tooltips on buttons.
	 * @var bool
	 */
	public $enableTooltips = true;

	/**
	 * @inheritdoc
	 */
	protected function initDefaultButtons()
	{
		parent::initDefaultButtons();

		foreach (['view', 'update', 'delete'] as $id) {
			if (!empty($this->{$id . 'ButtonIcon'}) && !isset($this->buttons[$id]['icon'])) {
				$this->buttons[$id]['icon'] = $this->{$id . 'ButtonIcon'};
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	protected function renderButton($id, $button, $row, $data)
	{
		if (!empty($button['icon'])) {
			if (!isset($button['options']['title']))
				$button['options']['title'] = isset($button['label']) ? $button['label'] : $id;
			$button['label'] = Html::icon($button['icon']);
			$button['imageUrl'] = null;
		}
		if ($this->enableTooltips) {
			$button['options']['data-toggle'] = 'tooltip';
		}

		parent::renderButton($id, $button, $row, $data);
	}
}
