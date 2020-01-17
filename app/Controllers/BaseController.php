<?php
namespace App\Controllers;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 *
 * @package CodeIgniter
 */

use App\Libraries\Html;
use App\Libraries\Theme;
use CodeIgniter\Controller;

class BaseController extends Controller
{

	/**
	 * An array of helpers to be loaded automatically upon
	 * class instantiation. These helpers will be available
	 * to all other controllers that extend BaseController.
	 *
	 * @var array
	 */
	protected $helpers = [
		'form',
		'html',

		'common'
	];

	/**
	 * @var Html $html
	 */
	protected $html;

	/**
	 * @var Theme $theme
	 */
	protected $theme;

	/**
	 * Constructor.
	 */
	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);

		//--------------------------------------------------------------------
		// Preload any models, libraries, etc, here.
		//--------------------------------------------------------------------
		// E.g.:
		// $this->session = \Config\Services::session();

		// set theme
		$this->theme = new Theme();
		$this->theme->setTheme(new \App\Libraries\Theme\Basic());

		// set html
		$this->html = \Config\Services::html();
		$this
			->html
			->addTitle('CMS Manana')
			->setCss($this->theme->getCss())
			->setJs($this->theme->getJs('header'), 'header')
			->setJs($this->theme->getJs('footer'), 'footer')
		;

		// set response content type
		$segments = $this->request->uri->getSegments();
		if (preg_match('/\.(?<format>json|xml|csv)$/', $segments[count($segments) - 1], $matches))
		{
			switch ($matches['format'])
			{
				case 'json' : $this->response->setContentType('application/json'); break;
				case 'xml'  : $this->response->setContentType('application/xml'); break;
				case 'csv'  : $this->response->setContentType('text/csv'); break;
			}
		}
	}

	/**
	 * _view
	 *
	 * @param   string  $name
	 * @param   array   $data
	 * @param   array   $options
	 *
	 * @return  string
	 */
	protected function _view(string $name, array $data = [], array $options = []): string
	{
		$contentType = $this->response->getHeaderLine('Content-Type');
		if (strpos($contentType, 'application/json') !== false)
		{
			// return json
			$config = new \Config\Format();
			return $config->getFormatter('application/json')->format($data);
		}
		elseif (strpos($contentType, 'application/xml') !== false)
		{
			// return xml
			$config = new \Config\Format();
			return $config->getFormatter('application/xml')->format($data);
		}
		elseif (strpos($contentType, 'text/csv') !== false)
		{
			// return csv
			$config = new \Config\Format();
			return $config->getFormatter('text/csv')->format($data);
		}

		// Content-Type: text/html
		$themeName = $this->theme->getThemeName();
		$page = $this->response->getBody() . view($themeName . '/' . $name, $data, $options);
		if ($this->theme->getLayout())
		{
			$layout = view(
				$themeName . '/layout/' . $this->theme->getLayout(),
				[
					'page'=>$page
				]
			);
		}
		else
			$layout = $page;

		return view(
			'html',
			[
				'layout'    => $layout
			]
		);
	}
}
