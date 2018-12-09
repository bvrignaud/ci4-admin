<?php namespace Admin\Controllers;

/**
 * Admin abstract controller file
 *
 * @package CI-Admin
 * @author  Benoit VRIGNAUD <benoit.vrignaud@zaclys.net>
 * @license https://opensource.org/licenses/MIT	MIT License
 * @link    http://github.com/bbvrignaud/ci-admin
 */

use CodeIgniter\Controller;

abstract class AbstractAdminController extends Controller
{
	/**
	 * IonAuth library
	 *
	 * @var \IonAuth\Libraries\IonAuth
	 */
	protected $ionAuth;

	/**
	 * Left menu
	 *
	 * @var array
	 */
	protected $leftMenu = [
		'dashboard'    => [
			'label' => 'Dashboard',
			'title' => 'Dashboard',
			'url'   => 'admin',
			'icon'  => 'dashboard',
		],
		'users'        => [
			'label' => 'Utilisateurs',
			'title' => 'Utilisateurs',
			'url'   => 'admin/users',
			'icon'  => 'user',
		],
		'informations' => [
			'label' => 'Admin.menu-labelInformation',
			'title' => 'Admin.menu-generalInformation',
			'url'   => 'admin/informations',
			'icon'  => 'info-circle',
		],
	];

	/**
	 * User
	 *
	 * @var stdClass
	 */
	protected $user;

	public function __construct()
	{
		$this->ionAuth = new \IonAuth\Libraries\IonAuth();
		if ($this->ionAuth->loggedIn())
		{
			$this->user = $this->ionAuth->user()->row();
		}
	}

	/**
	 * Check if user is logged in is admin
	 *
	 * @return boolean
	 */
	protected function isAuthorized(): bool
	{
		return $this->ionAuth->loggedIn() && $this->ionAuth->isAdmin();
	}

	/**
	 * Display the $body page inside the main vue
	 *
	 * @param string $body       Body vue
	 * @param string $pageTitle  Page title
	 * @param string $activeMenu Active menu
	 *
	 * @return string
	 */
	protected function view(string $body, string $pageTitle = '', string $activeMenu = ''): string
	{
		$mainData = [
			'appName'       => env('appName', 'CI-Admin'),
			'userFirstName' => $this->user->first_name,
			'userLastName'  => $this->user->last_name,
			'pageTitle'     => $pageTitle,
			'leftMenu'      => $this->displayLeftMenu($this->leftMenu, $activeMenu),
			'body'          => $body,
		];
		return view('Admin\main', $mainData);
	}

	/**
	 * Parse $menu and return the html menu
	 *
	 * @param array  $menus      Menu to parse
	 * @param string $activeMenu Active menu
	 *
	 * @return string
	 */
	private function displayLeftMenu(array $menus, string $activeMenu): string
	{
		$html = '';
		foreach ($menus as $keyMenu => $menu)
		{
			$active = $activeMenu === $keyMenu ? ' active' : '';
			$html  .= '<li class="nav-item"' . (empty($menu['title']) ? '' : ' title="' . lang($menu['title']) . '"') . '>';
			if (empty($menu['sous-menu']))
			{
				$html .= '<a class="nav-link ' . $active . '" href="' . site_url($menu['url']) . '">';
				$html .= isset($menu['icon']) ? '<i class="nav-icon fa fa-' . $menu['icon'] . '" aria-hidden="true"></i> ' : '';
				$html .= '<p>' . lang($menu['label']) . '</p>';
				$html .= '</a>';
			}
			else
			{
				$html .= self::displayLeftMenu($menu['sous-menu']);
			}
			$html .= '</li>';
		}
		return $html;
	}
}
