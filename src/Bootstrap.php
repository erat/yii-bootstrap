<?php

namespace Intersvyaz\YayBootstrap;

class Bootstrap extends \CApplicationComponent
{
	const ASSET_BOOTSTRAP = 'bootstrap';
	const ASSET_FONT_AWESOME = 'font-awesome';

	/**
	 * User-defined assets path.
	 * You can define as string
	 * @var string|array
	 */
	public $assetsPath;

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
	 * Global instance.
	 * @var self
	 */
	protected static $self;

	/**
	 * Path to bundled assets.
	 * @var string
	 */
	protected $defaultAssetsPath;

	/**
	 * Shortcut for asset manager object.
	 * @var \CAssetManager
	 */
	public $assetManager;

	/**
	 * Shortcut for client script object.
	 * @var \CClientScript
	 */
	public $clientScript;

	/**
	 * Save global instance.
	 */
	public function __construct()
	{
		if (empty(static::$self)) {
			static::$self = $this;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		static::$self->defaultAssetsPath = realpath(__DIR__ . '/../assets');
		static::$self->assetManager = &\Yii::app()->assetManager;
		static::$self->clientScript = &\Yii::app()->clientScript;
	}

	/**
	 * @return self
	 */
	public static function instance()
	{
		return static::$self;
	}

	/**
	 * @param string $assetName
	 * @return string Full path to asset.
	 * @throws \CException
	 */
	public function resolveAssetPath($assetName)
	{
		if (is_string(static::$self->assetsPath)) {
			$assetPath = \Yii::getPathOfAlias(static::$self->assetsPath) . DIRECTORY_SEPARATOR . $assetName;
			if (!is_dir($assetPath)) {
				$assetPath = null;
			}
		} elseif (is_array(static::$self->assetsPath) && isset(static::$self->assetsPath[$assetName])) {
			$assetPath = \Yii::getPathOfAlias(static::$self->assetsPath[$assetName]);
			if (!is_dir($assetPath)) {
				throw new \CException('Asset path not exists');
			}
		} else {
			$assetPath = null;
		}

		if (empty($assetPath)) {
			$assetPath = static::$self->defaultAssetsPath . DIRECTORY_SEPARATOR . $assetName;
		}

		return $assetPath;
	}

	/**
	 * Register bootstrap styles.
	 * @return self
	 */
	public function registerStyleAssets()
	{
		$assetPath = static::$self->resolveAssetPath(static::ASSET_BOOTSTRAP);
		$assetUrl = static::$self->assetManager->publish($assetPath);

		$responsivePrefix = static::$self->responsive ? '' : '.no-responsive';
		$minifyPrefix = static::$self->minifyAssets ? '.min' : '';

		static::$self->clientScript
			->registerCssFile($assetUrl . '/css/bootstrap' . $responsivePrefix . $minifyPrefix . '.css')
			->registerCssFile($assetUrl . '/css/bootstrap.yii.css');

		return static::$self;
	}

	/**
	 * Register bootstrap scripts.
	 * @return self
	 */
	public function registerScriptAssets()
	{
		$assetPath = static::$self->resolveAssetPath(static::ASSET_BOOTSTRAP);
		$assetUrl = static::$self->assetManager->publish($assetPath);

		$minifyPrefix = static::$self->minifyAssets ? '.min' : '';

		static::$self->clientScript
			->registerScriptFile($assetUrl . '/js/bootstrap' . $minifyPrefix . '.js');

		return static::$self;
	}

	/**
	 * Register font-awesome assets.
	 * @return self
	 */
	public function registerFontAwesomeAssets()
	{
		$assetPath = static::$self->resolveAssetPath(static::ASSET_FONT_AWESOME);
		$assetUrl = static::$self->assetManager->publish($assetPath);

		$minifyPrefix = static::$self->minifyAssets ? '.min' : '';

		static::$self->clientScript
			->registerCssFile($assetUrl . '/css/font-awesome' . $minifyPrefix . '.css');

		return static::$self;
	}
}