<?php

namespace Intersvyaz\Bootstrap;

/**
 * Bootstrap styled Yii's {@link CHtml} with some additional stuff.
 */
class Html extends \CHtml
{
	/**
	 * Generates an bootstrap icon.
	 * Example: <pre>echo Html::icon('calendar');<pre>
	 * @param string $icon The icon name.
	 * @param array $htmlOptions additional HTML attributes.
	 * @param string $tag Icon html tag.
	 * @return string The generated icon.
	 * @see http://getbootstrap.com/2.3.2/base-css.html#icons
	 */
	public static function icon($icon, $htmlOptions = [], $tag = 'i')
	{
		static::addCssClass($htmlOptions, 'icon-' . $icon);

		return static::tag($tag, $htmlOptions, '');
	}

	/**
	 * Bootstrap progress bar.
	 * Example: <pre>echo Html::progressBar([['percent'=>80]]);<pre>
	 * @param array $pieces One or more bar piece. Each piece have options:
	 * <ul>
	 *   <li>'percent' - piece with in percent (required)</li>
	 *   <li>'type' - type of piece (optional)</li>
	 *   <li>'content' - inner content of piece (optional)</li>
	 * </ul>
	 * @param array $htmlOptions Wrapper options.
	 * @return string Generated progress bar.
	 * @see http://getbootstrap.com/2.3.2/components.html#progress
	 */
	public static function progressBar($pieces, $htmlOptions = [])
	{
		if (empty($pieces))
			return '';

		$output = '';
		static::addCssClass($htmlOptions, 'progress');
		$output .= static::tag('div', $htmlOptions, false, false);
		foreach ($pieces as $piece) {
			$typeClass = empty($piece['type']) ? '' : ' bar-' . $piece['type'];
			$content = empty($piece['content']) ? '' : $piece['content'];
			$output .= static::tag('div', [
				'class' => 'bar' . $typeClass,
				'style' => 'width:' . $piece['percent'] . '%'
			], $content);
		}

		return $output . '</div>';
	}

	/**
	 * @inheritdoc
	 */
	public static function errorSummary($model, $header = null, $footer = null, $htmlOptions = array())
	{
		static::addCssClass($htmlOptions, 'alert alert-block alert-error');
		return parent::errorSummary($model, $header, $footer, $htmlOptions);
	}

	/**
	 * Utility function for appending class names for a generic $htmlOptions array.
	 * @param array $htmlOptions
	 * @param string $class
	 */
	public static function addCssClass(&$htmlOptions, $class)
	{
		if (empty($class)) {
			return;
		}

		if (isset($htmlOptions['class'])) {
			$htmlOptions['class'] .= ' ' . $class;
		} else {
			$htmlOptions['class'] = $class;
		}
	}
}

