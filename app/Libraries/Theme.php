<?php namespace App\Libraries;

use App\Libraries\Theme\ThemeInterface;

/**
 * Class Theme
 *
 * @package App\Libraries
 */
class Theme
{
	/**
	 * @var ThemeInterface $theme
	 */
	private $theme;

	/**
	 * @var string $layout
	 */
	private $layout = 'default';

	/**
	 * @var array $css
	 */
	private $css = [];

	/**
	 * @var array $js
	 */
	private $js = [
		'header'=>[],
		'footer'=>[]
	];

	/**
	 * setTheme
	 *
	 * @param   ThemeInterface      $theme
	 *
	 * @return  Theme
	 */
	public function setTheme(ThemeInterface $theme): Theme
	{
		// set Theme
		$this->theme = $theme;

		// set CSS
		$this->setCss($this->theme->getCss());

		// set javascript
		$this->setJs($this->theme->getJs('header'), 'header');
		$this->setJs($this->theme->getJs('footer'), 'footer');

		return $this;
	}

	/**
	 * getThemeName
	 *
	 * @return  string
	 */
	public function getThemeName(): string
	{
		try
		{
			$themeName = (new \ReflectionClass(get_class($this->theme)))->getShortName();
			$themeName = strtolower($themeName);
		}
		catch (\ReflectionException $e)
		{
			die($e->getCode() . ' : ' . $e->getMessage());
		}

		return $themeName;
	}

	/**
	 * setLayout
	 *
	 * @param   string      $layout
	 *
	 * @return  Theme
	 */
	public function setLayout(string $layout): Theme
	{
		$this->layout = $layout;
		return $this;
	}

	/**
	 * getLayout
	 *
	 * @return  string
	 */
	public function getLayout(): string
	{
		return $this->layout;
	}

	/**
	 * setCss
	 *
	 * @param   array       $css
	 *
	 * @return  Theme
	 */
	public function setCss(array $css): Theme
	{
		$this->css = $css;
		return $this;
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
	 * setJs
	 *
	 * @param   array       $js
	 * @param   string      $position
	 *
	 * @return  Theme
	 */
	public function setJs(array $js, string $position = 'footer'): Theme
	{
		$this->js[$position] = $js;
		return $this;
	}

	/**
	 * getJs
	 *
	 * @param   string      $position
	 *
	 * @return  array
	 */
	public function getJs(string $position = 'footer'): array
	{
		return $this->js[$position];
	}
}
