<?php namespace Admin\Controllers;

/**
 * Admin abstract controller file
 *
 * @package CI-Admin
 * @author  Benoit VRIGNAUD <benoit.vrignaud@zaclys.net>
 * @license https://opensource.org/licenses/MIT	MIT License
 * @link    http://github.com/bbvrignaud/ci-admin
 */

class Home extends AbstractAdminController
{
	/**
	 * Affiche la page d'entrée du site en fonction du statut de l'utilisateur (non connecté, gamer ou leader)
	 *
	 * @return \CodeIgniter\HTTP\RedirectResponse|string
	 */
	public function index()
	{
		if (! $this->isAuthorized())
		{
			return redirect()->to('/');
		}
		return $this->view('home', lang('Admin.home-title'));
	}
}
