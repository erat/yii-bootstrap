<?php

class WidgetTestHelper
{
	/**
	 * @param $widget
	 * @return string
	 */
	public static function runAndCapture($widget)
	{
		$widget->init();
		ob_start();
		$widget->run();
		return ob_get_clean();
	}
}