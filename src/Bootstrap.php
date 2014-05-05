<?php

namespace Intersvyaz\Bootstrap;

class Bootstrap extends \CApplicationComponent
{
	/**
	 * User-defined assets path alias.
	 * @var string
	 */
	public $assetsAlias;

	/**
	 * @var bool
	 */
	public $minifyAssets = true;

	/**
	 * Whether to use bootstrap responsive styles.
	 * @var bool
	 */
	public $responsive = true;

	/**
	 * Assets path.
	 * @var
	 */
	protected $assetsPath;

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		if(isset($this->assetsAlias)) {
			$this->assetsPath = \Yii::getPathOfAlias($this->assetsAlias);
		} else {
			$this->assetsPath = realpath(__DIR__ . '/../assets');
		}
	}

	/**
	 * Register bootstrap styles.
	 * @return self
	 */
	public function registerStyleAssets()
	{
		$assetUrl = \Yii::app()->assetManager->publish($this->assetsPath);
		$responsivePrefix = $this->responsive ? '.responsive' : '';
		$minifyPrefix = $this->minifyAssets ? '.min' : '';

		$cs = \Yii::app()->clientScript;
		$cs->registerCssFile($assetUrl . '/css/bootstrap' . $responsivePrefix . $minifyPrefix . '.css')
			->registerCssFile($assetUrl . '/css/bootstrap.yii.css');

		if ($this->responsive) {
			$cs->registerMetaTag('width=device-width, initial-scale=1.0', 'viewport');
		}

		return $this;
	}

	/**
	 * Register bootstrap scripts.
	 * @return self
	 */
	public function registerScriptAssets()
	{
		$assetUrl = \Yii::app()->assetManager->publish($this->assetsPath);
		$minifyPrefix = $this->minifyAssets ? '.min' : '';

		\Yii::app()->clientScript
			->registerScriptFile($assetUrl . '/js/bootstrap' . $minifyPrefix . '.js');

		return $this;
	}
}
