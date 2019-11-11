<?php namespace App\Libraries;

/**
 * Class Html
 *
 * @package App\Libraries
 */
class Html
{
	/**
	 * @var array $title
	 */
	private $title = [];

	/**
	 * @var array $meta
	 */
	private $meta = [];

	/**
	 * @var string $description
	 */
	private $description = '';

	/**
	 * @var string $image
	 */
	private $image = '';

	/**
	 * @var array $bodyAttribute
	 */
	private $bodyAttribute = [];

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
	 * @var string $assetsFolder
	 */
	private $assetsFolder = 'assets/';

	/**
	 * setTitle
	 *
	 * @param   array       $title
	 *
	 * @return  Html
	 */
	public function setTitle(array $title): Html
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * addTitle
	 *
	 * @param   string      $title
	 *
	 * @return  Html
	 */
	public function addTitle(string $title): Html
	{
		$this->title[] = $title;
		return $this;
	}

	/**
	 * getTitle
	 *
	 * @return  array
	 */
	public function getTitle(): array
	{
		return $this->title;
	}

	/**
	 * getTitleString
	 *
	 * @param   string      $glue
	 *
	 * @return  string
	 */
	public function getTitleString(string $glue = ' < '): string
	{
		$title = $this->title;
		krsort($title);
		return implode($glue, $title);
	}

	/**
	 * setSiteName
	 *
	 * @param   string      $siteName
	 *
	 * @return  Html
	 */
	public function setSiteName(string $siteName): Html
	{
		$this->title[0] = $siteName;
		return $this;
	}

	/**
	 * getSiteName
	 *
	 * @return  string
	 */
	public function getSiteName(): string
	{
		return $this->title[0]??'';
	}

	/**
	 * setMeta
	 *
	 * @param   array       $attribute
	 *
	 * @return  Html
	 */
	public function setMeta(array $attribute): Html
	{
		$this->meta = $attribute;
		return $this;
	}

	/**
	 * addMeta
	 *
	 * @param   array       $attribute
	 *
	 * @return  Html
	 */
	public function addMeta(array $attribute): Html
	{
		$this->meta[] = $attribute;
		return $this;
	}

	/**
	 * getMeta
	 *
	 * @return  array
	 */
	public function getMeta(): array
	{
		return $this->meta;
	}

	/**
	 * setDescription
	 *
	 * @param   string      $description
	 *
	 * @return  Html
	 */
	public function setDescription(string $description): Html
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * getDescription
	 *
	 * @return  string
	 */
	public function getDescription(): string
	{
		return $this->description;
	}

	/**
	 * setImage
	 *
	 * @param   string      $image
	 *
	 * @return  Html
	 */
	public function setImage(string $image): Html
	{
		$this->image = $image;
		return $this;
	}

	/**
	 * getImage
	 *
	 * @return  string
	 */
	public function getImage(): string
	{
		return $this->image;
	}

	/**
	 * setBodyAttribute
	 *
	 * @param   array       $attribute
	 *
	 * @return  Html
	 */
	public function setBodyAttribute(array $attribute): Html
	{
		$this->bodyAttribute = $attribute;
		return $this;
	}

	/**
	 * addBodyAttribute
	 *
	 * @param   string      $key
	 * @param   string      $value
	 *
	 * @return  Html
	 */
	public function addBodyAttribute(string $key, string $value): Html
	{
		$this->bodyAttribute[$key] = $value;
		return $this;
	}

	/**
	 * getBodyAttribute
	 *
	 * @return  array
	 */
	public function getBodyAttribute(): array
	{
		return $this->bodyAttribute;
	}

	/**
	 * setCss
	 *
	 * @param   array       $css
	 *
	 * @return  Html
	 */
	public function setCss(array $css): Html
	{
		$this->css = $css;
		return $this;
	}

	/**
	 * addCss
	 *
	 * @param   string      $css
	 *
	 * @return  Html
	 */
	public function addCss(string $css): Html
	{
		if (!in_array($css, $this->css))
			$this->css[] = $css;
		return $this;
	}

	/**
	 * getCss
	 *
	 * @return  array
	 */
	public function getCss(): array
	{
		$css = $this->css;

		foreach ($css as $key => $value)
		{
			$tmp = mb_substr($value, 0, 2);
			if (
				$tmp === '//'
				&& $tmp === 'ht'
			)
			{
				// pass
			}
			elseif (is_file(FCPATH . $this->assetsFolder . $value))
			{
				$css[$key] = $this->assetsFolder . $value . '?ver=' . filemtime(FCPATH . $this->assetsFolder . $value);
			}
		}

		return $css;
	}

	/**
	 * setJs
	 *
	 * @param   array       $js
	 * @param   string      $position
	 *
	 * @return  Html
	 */
	public function setJs(array $js, string $position = 'footer'): Html
	{
		$this->js[$position] = $js;
		return $this;
	}

	/**
	 * addJs
	 *
	 * @param   string      $js
	 * @param   string      $position
	 *
	 * @return  Html
	 */
	public function addJs(string $js, string $position = 'footer'): Html
	{
		if (!isset($this->js[$position]))
			$this->js[$position] = [];

		if (!in_array($js, $this->js[$position]))
			$this->js[$position][] = $js;

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
		$js = $this->js[$position]??[];

		foreach ($js as $key => $value)
		{
			$tmp = mb_substr($value, 0, 2);
			if (
				$tmp === '//'
				&& $tmp === 'ht'
			)
			{
				// pass
			}
			elseif (is_file(FCPATH . $this->assetsFolder . $value))
			{
				$js[$key] = $this->assetsFolder . $value . '?ver=' . filemtime(FCPATH . $this->assetsFolder . $value);
			}
		}

		return $js;
	}
}
