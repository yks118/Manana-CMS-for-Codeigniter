<?php
namespace App\Libraries\Theme;

/**
 * Class BlackDashboard
 *
 * @package App\Libraries\Theme
 *
 * @url https://github.com/creativetimofficial/black-dashboard
 *
 * @license MIT
 */
class BlackDashboard implements ThemeInterface
{
	/**
	 * @var array $css
	 */
	private $css = [
		'https://fonts.googleapis.com/css?family=Poppins:200,300,400,600,700,800',
		// Nucleo Icons
		'https://cdn.manana.kr/theme/black dashboard/css/nucleo-icons.css',
		// theme css
		'https://cdn.manana.kr/theme/black dashboard/css/black-dashboard.min.css',
		// demo css
		'https://cdn.manana.kr/theme/black dashboard/demo/demo.css'
	];

	/**
	 * @var array $js
	 */
	private $js = [
		'header'    => [],
		'footer'    => [
			'https://cdn.manana.kr/vendors/jquery/jquery.min.js',
			'https://cdn.manana.kr/vendors/popper.js/popper.min.js',
			'https://cdn.manana.kr/vendors/bootstrap/js/bootstrap.min.js',
			'https://cdn.manana.kr/vendors/perfect-scrollbar/perfect-scrollbar.min.js',
			'https://cdn.manana.kr/vendors/bootstrap-notify/bootstrap-notify.min.js',
			'https://cdn.manana.kr/theme/black dashboard/js/black-dashboard.min.js',
			'https://cdn.manana.kr/theme/black dashboard/demo/demo.js'
		]
	];

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
