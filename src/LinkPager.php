<?php

namespace Intersvyaz\Bootstrap;

/**
 * Bootstrap styled Yii's {@link \CLinkPager}.
 * @see http://getbootstrap.com/2.3.2/components.html#pagination
 */
class LinkPager extends \CLinkPager
{
	/**
	 * @inheritdoc
	 */
	public $hiddenPageCssClass = 'disabled';

	/**
	 * @inheritdoc
	 */
	public $selectedPageCssClass = 'active';

	/**
	 * @inheritdoc
	 */
	public $nextPageLabel = '&raquo;';

	/**
	 * @inheritdoc
	 */
	public $prevPageLabel = '&laquo;';

	/**
	 * @inheritdoc
	 */
	public $header = '';

	/**
	 * @inheritdoc
	 */
	public $cssFile = false;

	/**
	 * @var array Options for wrapper tag.
	 */
	public $wrapperOptions = ['class' => 'pagination'];

	/**
	 * @inheritdoc
	 */
	public function run()
	{
		echo Html::tag('div', $this->wrapperOptions, false, false);
		parent::run();
		echo Html::closeTag('div');
	}

	/**
	 * @inheritdoc
	 */
	public function registerClientScript()
	{
		if ($this->cssFile !== false)
			\Yii::app()->clientScript->registerCssFile($this->cssFile);
	}
}