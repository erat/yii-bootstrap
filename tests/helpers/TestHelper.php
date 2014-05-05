<?php

class TestHelper
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

	/**
	 * @param object $object
	 * @param string $propertyName
	 * @return mixed
	 */
	public static function getPropValue($object, $propertyName)
	{
		$prop = new ReflectionProperty(get_class($object), $propertyName);
		$prop->setAccessible(true);
		return $prop->getValue($object);
	}
}