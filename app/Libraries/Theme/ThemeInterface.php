<?php namespace App\Libraries\Theme;

use App\Libraries\HtmlPosition;

/**
 * Interface ThemeInterface
 *
 * @package App\Libraries\Theme
 */
interface ThemeInterface
{
	/**
	 * getCss
	 *
	 * @return array
	 */
	public function getCss(): array;

	/**
	 * getJs
	 *
	 * @param   string  $position
	 *
	 * @return  array
	 */
	public function getJs(string $position = 'footer'): array;
}
