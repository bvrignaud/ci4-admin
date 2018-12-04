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
		return $this->ionAuth->loggedIn() && $this->ionAuth->inGroup('admin');
	}

	/**
	 * Affiche la page souhaitée ainsi que les headers
	 *
	 * @param string $name      Vue name
	 * @param string $pageTitle Page title
	 * @param array  $data      Tableau de paramètres
	 *
	 * @return void
	 */
	protected function view(string $name, string $pageTitle = '', array $data = []): void
	{
		$mainData = [
			'appName'       => env('appName', 'CI-Admin'),
			'userFirstName' => $this->user->first_name,
			'userLastName'  => $this->user->last_name,
			'pageTitle'     => $pageTitle,
			'body'          => view("Admin\\$name", $data),
		];
		echo view('Admin\main', $mainData);
	}
}
