<?php

namespace Intersvyaz\YayBootstrap;

class Html extends \CHtml
{
	/**
	 * Generates an bootstrap icon.
	 * @param string $icon The icon name.
	 * @param array $htmlOptions additional HTML attributes.
	 * @param string $tag Icon html tag.
	 * @return string The generated icon.
	 * @see http://getbootstrap.com/2.3.2/base-css.html#icons
	 */
	public static function icon($icon, $htmlOptions = [], $tag = 'i')
	{
		static::addCssClass($htmlOptions, 'icon-' . $icon);
		return static::tag($tag, $htmlOptions);
	}

	/**
	 * Generates an font-awesome icon.
	 * @param string $icon The icon name.
	 * @param array $htmlOptions Additional HTML attributes.
	 * @param string $tag Icon html tag.
	 * @return string The generated icon.
	 * @see http://fortawesome.github.io/Font-Awesome/examples/
	 */
	public static function faIcon($icon, $htmlOptions = [], $tag = 'i')
	{
		static::addCssClass($htmlOptions, 'fa fa-' . $icon);
		return static::tag($tag, $htmlOptions);
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

