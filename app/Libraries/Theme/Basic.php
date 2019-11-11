<?php namespace App\Libraries\Theme;

/**
 * Class Basic
 *
 * @package App\Libraries\Theme
 */
class Basic implements ThemeInterface
{
	/**
	 * @var array $css
	 */
	private $css = [];

	/**
	 * @var array $js
	 */
	private $js = [
		'header'    => [],
		'footer'    => []
	];

	public function __construct()
	{
		// set css
		$this->css = [
			cdn_url('')
		];

		// set js
		$this->js = [
			'header'    => [],
			'footer'    => [
				cdn_url('')
			]
		];
	}

	/**
	 * getCss
	 *
	 * @return  array
	 */
	public function getCss(): array
	{
		return $this->css;
	}

	/**
	 * getJs
	 *
	 * @param   string  $position
	 *
	 * @return  array
	 */
	public function getJs(string $position = 'footer'): array
	{
		return $this->js[$position];
	}
}
