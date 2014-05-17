<?php

namespace Intersvyaz\Bootstrap;

\Yii::import('zii.widgets.grid.CGridView');

/**
 * Bootstrap styled Yii's {@link \CGridView}.
 */
class GridView extends \CGridView
{
	/**
	 * @inheritdoc
	 */
	public $pager = ['class' => '\Intersvyaz\Bootstrap\LinkPager'];

	/**
	 * @inheritdoc
	 */
	public $pagerCssClass = 'pagination';

	/**
	 * @inheritdoc
	 */
	public $cssFile = false;

	/**
	 * @inheritdoc
	 */
	public $itemsCssClass = 'items table';
}