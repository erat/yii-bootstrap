<?php

namespace Intersvyaz\YayBootstrap;

\Yii::import('zii.widgets.CBreadcrumbs');

/**
 * Bootstrap breadcrumb widget.
 * @see http://getbootstrap.com/2.3.2/components.html#breadcrumbs
 */
class Breadcrumbs extends \CBreadcrumbs
{
	/**
	 * @inheritdoc
	 */
	public $tagName = 'ul';

	/**
	 * @inheritdoc
	 */
	public $htmlOptions = ['class' => 'breadcrumb'];

	/**
	 * @inheritdoc
	 */
	public $inactiveLinkTemplate = '{label}';

	/**
	 * @inheritdoc
	 */
	public $separator = '/';

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		$this->separator = '<span class="divider">' . $this->separator . '</span>';
		if ($this->homeLink === null) {
			$this->homeLink = Html::link(\Yii::t('zii', 'Home'), \Yii::app()->homeUrl);
		}
	}

	/**
	 * @inheritdoc
	 */
	public function run()
	{
		if (empty($this->links))
			return;

		echo Html::openTag($this->tagName, $this->htmlOptions);

		if ($this->homeLink !== false) {
			// check whether home link is not a link
			$active = (stripos($this->homeLink, '<a') === false) ? ' class="active"' : '';
			echo '<li' . $active . '>' . $this->homeLink . $this->separator . '</li>';
		}

		end($this->links);
		$lastLink = key($this->links);

		foreach ($this->links as $label => $url) {
			if (is_string($label) || is_array($url)) {
				echo '<li>';
				echo strtr($this->activeLinkTemplate, [
					'{url}' => Html::normalizeUrl($url),
					'{label}' => $this->encodeLabel ? Html::encode($label) : $label,
				]);
			} else {
				echo '<li class="active">';
				echo str_replace('{label}', $this->encodeLabel ? Html::encode($url) : $url, $this->inactiveLinkTemplate);
			}

			if ($lastLink !== $label) {
				echo $this->separator;
			}
			echo '</li>';
		}

		echo Html::closeTag($this->tagName);
	}
}

