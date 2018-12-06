<?php namespace Admin\Controllers;

/**
 * Admin/Users controller file
 *
 * @package CI-Admin
 * @author  Benoit VRIGNAUD <benoit.vrignaud@zaclys.net>
 * @license https://opensource.org/licenses/MIT	MIT License
 * @link    http://github.com/bbvrignaud/ci-admin
 */

class Users extends AbstractAdminController
{

	/**
	 * Display informations page
	 *
	 * @return \CodeIgniter\HTTP\RedirectResponse|string
	 */
	public function index()
	{
		if (! $this->isAuthorized())
		{
			return redirect()->to('/');
		}

		$data = [
			'message' => session()->getFlashdata('message'),
			'message' => '',
			'users'   => $this->ionAuth->users()->result(),
		];

		foreach ($data['users'] as $k => $user)
		{
			$data['users'][$k]->groups = $this->ionAuth->getUsersGroups($user->id)->getResult();
		}

		$body = view('Admin\users', $data);

		return $this->view($body, lang('Auth.index_heading'), 'users');
	}

}
