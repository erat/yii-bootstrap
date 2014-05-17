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
	 * @inheritdoc
	 */
	public function registerClientScript()
	{
		if ($this->cssFile !== false)
			\Yii::app()->clientScript->registerCssFile($this->cssFile);
	}
}