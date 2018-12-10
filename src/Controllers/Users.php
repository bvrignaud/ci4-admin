<?php namespace Admin\Controllers;

/**
 * Admin/Users controller file
 *
 * @package CI-Admin
 * @author  Benoit VRIGNAUD <benoit.vrignaud@zaclys.net>
 * @license https://opensource.org/licenses/MIT	MIT License
 * @link    http://github.com/bvrignaud/ci-admin
 */

class Users extends AbstractAdminController
{
	/**
	 * Configuration
	 *
	 * @var \IonAuth\Config\IonAuth
	 */
	private $configIonAuth;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->configIonAuth = config('IonAuth');
	}

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
			'users'   => $this->ionAuth->users()->result(),
		];

		foreach ($data['users'] as $k => $user)
		{
			$data['users'][$k]->groups = $this->ionAuth->getUsersGroups($user->id)->getResult();
		}

		$body = view('Admin\users\users', $data);

		return $this->view($body, lang('Auth.index_heading'), 'users');
	}

	/**
	 * Create a new user
	 *
	 * @return string|\CodeIgniter\HTTP\RedirectResponse
	 */
	public function createUser()
	{
		if (! $this->isAuthorized())
		{
			return redirect()->to('/');
		}

		$data['title'] = lang('Auth.create_user_heading');

		$tables                  = $this->configIonAuth->tables;
		$identityColumn          = $this->configIonAuth->identity;
		$data['identity_column'] = $identityColumn;

		// validate form input
		$validation = \Config\Services::validation();
		$validation->setRule('first_name', lang('Auth.create_user_validation_fname_label'), 'trim|required');
		$validation->setRule('last_name', lang('Auth.create_user_validation_lname_label'), 'trim|required');
		if ($identityColumn !== 'email')
		{
			$validation->setRule('identity', lang('Auth.create_user_validation_identity_label'), 'trim|required|is_unique[' . $tables['users'] . '.' . $identityColumn . ']');
			$validation->setRule('email', lang('Auth.create_user_validation_email_label'), 'trim|required|valid_email');
		}
		else
		{
			$validation->setRule('email', lang('Auth.create_user_validation_email_label'), 'trim|required|valid_email|is_unique[' . $tables['users'] . '.email]');
		}
		$validation->setRule('phone', lang('Auth.create_user_validation_phone_label'), 'trim');
		$validation->setRule('company', lang('Auth.create_user_validation_company_label'), 'trim');
		$validation->setRule('password', lang('Auth.create_user_validation_password_label'), 'required|min_length[' . $this->configIonAuth->minPasswordLength . ']|matches[password_confirm]');
		$validation->setRule('password_confirm', lang('Auth.create_user_validation_password_confirm_label'), 'required');

		if ($this->request->getPost() && $validation->withRequest($this->request)->run())
		{
			$email    = strtolower($this->request->getPost('email'));
			$identity = ($identityColumn === 'email') ? $email : $this->request->getPost('identity');
			$password = $this->request->getPost('password');

			$additionalData = [
				'first_name' => $this->request->getPost('first_name'),
				'last_name'  => $this->request->getPost('last_name'),
				'company'    => $this->request->getPost('company'),
				'phone'      => $this->request->getPost('phone'),
			];
		}
		if ($this->request->getPost() && $validation->withRequest($this->request)->run() && $this->ionAuth->register($identity, $password, $email, $additionalData))
		{
			// check to see if we are creating the user
			// redirect them back to the admin page
			session()->setFlashdata('message', $this->ionAuth->messages());
			return redirect()->to('/admin/users');
		}
		else
		{
			// display the create user form
			helper(['form']);
			// set the flash data error message if there is one
			$data['message'] = $validation->getErrors() ? $validation->listErrors() : ($this->ionAuth->errors() ? $this->ionAuth->errors() : session()->getFlashdata('message'));

			$data['first_name'] = [
				'name'  => 'first_name',
				'id'    => 'first_name',
				'type'  => 'text',
				'value' => set_value('first_name'),
			];
			$data['last_name'] = [
				'name'  => 'last_name',
				'id'    => 'last_name',
				'type'  => 'text',
				'value' => set_value('last_name'),
			];
			$data['identity'] = [
				'name'  => 'identity',
				'id'    => 'identity',
				'type'  => 'text',
				'value' => set_value('identity'),
			];
			$data['email'] = [
				'name'  => 'email',
				'id'    => 'email',
				'type'  => 'email',
				'value' => set_value('email'),
			];
			$data['company'] = [
				'name'  => 'company',
				'id'    => 'company',
				'type'  => 'text',
				'value' => set_value('company'),
			];
			$data['phone'] = [
				'name'  => 'phone',
				'id'    => 'phone',
				'type'  => 'text',
				'value' => set_value('phone'),
			];
			$data['password'] = [
				'name'  => 'password',
				'id'    => 'password',
				'type'  => 'password',
				'value' => set_value('password'),
			];
			$data['password_confirm'] = [
				'name'  => 'password_confirm',
				'id'    => 'password_confirm',
				'type'  => 'password',
				'value' => set_value('password_confirm'),
			];

			$body = view('Admin\users\create_user', $data);
			return $this->view($body, lang('Auth.create_user_heading'), 'users');
		}
	}

	/**
	 * Edit a user
	 *
	 * @param integer $id User id
	 *
	 * @return string string|\CodeIgniter\HTTP\RedirectResponse
	 */
	public function edit(int $id)
	{
		if (! $this->isAuthorized() && ! ($this->ionAuth->user()->row()->id == $id))
		{
			return redirect()->to('/admin/users');
		}

		$validation = \Config\Services::validation();

		$data['title'] = lang('Auth.edit_user_heading');

		$user          = $this->ionAuth->user($id)->row();
		$groups        = $this->ionAuth->groups()->resultArray();
		$currentGroups = $this->ionAuth->getUsersGroups($id)->getResult();

		if (! empty($_POST))
		{
			// validate form input
			$validation->setRule('first_name', lang('Auth.edit_user_validation_fname_label'), 'trim|required');
			$validation->setRule('last_name', lang('Auth.edit_user_validation_lname_label'), 'trim|required');
			$validation->setRule('phone', lang('Auth.edit_user_validation_phone_label'), 'trim|required');
			$validation->setRule('company', lang('Auth.edit_user_validation_company_label'), 'trim|required');

			// do we have a valid request?
			if ($id !== $this->request->getPost('id', FILTER_VALIDATE_INT))
			{
				throw new \Exception(lang('Auth.error_security'));
			}

			// update the password if it was posted
			if ($this->request->getPost('password'))
			{
				$validation->setRule('password', lang('Auth.edit_user_validation_password_label'), 'required|min_length[' . $this->configIonAuth->minPasswordLength . ']|matches[password_confirm]');
				$validation->setRule('password_confirm', lang('Auth.edit_user_validation_password_confirm_label'), 'required');
			}

			if ($this->request->getPost() && $validation->withRequest($this->request)->run())
			{
				$data = [
					'first_name' => $this->request->getPost('first_name'),
					'last_name'  => $this->request->getPost('last_name'),
					'company'    => $this->request->getPost('company'),
					'phone'      => $this->request->getPost('phone'),
				];

				// update the password if it was posted
				if ($this->request->getPost('password'))
				{
					$data['password'] = $this->request->getPost('password');
				}

				// Only allow updating groups if user is admin
				if ($this->ionAuth->isAdmin())
				{
					// Update the groups user belongs to
					$groupData = $this->request->getPost('groups');

					if (! empty($groupData))
					{
						$this->ionAuth->removeFromGroup('', $id);

						foreach ($groupData as $grp)
						{
							$this->ionAuth->addToGroup($grp, $id);
						}
					}
				}

				// check to see if we are updating the user
				if ($this->ionAuth->update($user->id, $data))
				{
					session()->setFlashdata('message', $this->ionAuth->messages());
				}
				else
				{
					session()->setFlashdata('message', $this->ionAuth->errors($validationListTemplate));
				}
				return redirect()->to('/admin/users');
			}
		}

		// display the edit user form
		helper(['form']);

		// set the flash data error message if there is one
		$data['message'] = $validation->getErrors() ? $validation->listErrors() : ($this->ionAuth->errors() ? $this->ionAuth->errors() : $session->getFlashdata('message'));

		// pass the user to the view
		$data['user']          = $user;
		$data['groups']        = $groups;
		$data['currentGroups'] = $currentGroups;

		$data['first_name'] = [
			'name'  => 'first_name',
			'id'    => 'first_name',
			'type'  => 'text',
			'value' => set_value('first_name', $user->first_name ?: ''),
		];
		$data['last_name'] = [
			'name'  => 'last_name',
			'id'    => 'last_name',
			'type'  => 'text',
			'value' => set_value('last_name', $user->last_name ?: ''),
		];
		$data['company'] = [
			'name'  => 'company',
			'id'    => 'company',
			'type'  => 'text',
			'value' => set_value('company', empty($user->company) ? '' : $user->company),
		];
		$data['phone'] = [
			'name'  => 'phone',
			'id'    => 'phone',
			'type'  => 'text',
			'value' => set_value('phone', empty($user->phone) ? '' : $user->phone),
		];
		$data['password'] = [
			'name' => 'password',
			'id'   => 'password',
			'type' => 'password',
		];
		$data['password_confirm'] = [
			'name' => 'password_confirm',
			'id'   => 'password_confirm',
			'type' => 'password',
		];
		$data['ionAuth'] = $this->ionAuth;

		$body = view('Admin\users\edit_user', $data);
		return $this->view($body, lang('Auth.edit_user_heading'), 'users');
	}

	/**
	 * Activate the user
	 *
	 * @param integer $id The user ID
	 *
	 * @return \CodeIgniter\HTTP\RedirectResponse
	 */
	public function activate(int $id): \CodeIgniter\HTTP\RedirectResponse
	{
		$this->ionAuth->activate($id);
		session()->setFlashdata('message', $this->ionAuth->messages());
		return redirect()->to('/admin/users');
	}

	/**
	 * Deactivate the user
	 *
	 * @param integer $id The user ID
	 *
	 * @throw Exception
	 *
	 * @return string|\CodeIgniter\HTTP\RedirectResponse
	 */
	public function deactivate(int $id = 0)
	{
		if (! $this->isAuthorized())
		{
			// redirect them to the home page because they must be an administrator to view this
			throw new \Exception('You must be an administrator to view this page.');
		}

		$validation = \Config\Services::validation();

		$validation->setRule('confirm', lang('Auth.deactivate_validation_confirm_label'), 'required');
		$validation->setRule('id', lang('Auth.deactivate_validation_user_id_label'), 'required|integer');

		if (! $validation->withRequest($this->request)->run())
		{
			helper(['form']);
			$data['user'] = $this->ionAuth->user($id)->row();
			$body         = view('Admin\users\deactivate_user', $data);
			return $this->view($body, lang('Auth.deactivate_heading'), 'users');
		}
		else
		{
			// do we really want to deactivate?
			if ($this->request->getPost('confirm') === 'yes')
			{
				// do we have a valid request?
				if ($id !== $this->request->getPost('id', FILTER_VALIDATE_INT))
				{
					throw new \Exception(lang('Auth.error_security'));
				}

				// do we have the right userlevel?
				if ($this->ionAuth->loggedIn() && $this->ionAuth->isAdmin())
				{
					$message = $this->ionAuth->deactivate($id) ? $this->ionAuth->messages() : $this->ionAuth->errors();
					session()->setFlashdata('message', $message);
				}
			}

			// redirect them back to the auth page
			return redirect()->to('/admin/users');
		}
	}

	/**
	 * Edit a group
	 *
	 * @param integer $id Group id
	 *
	 * @return string|CodeIgniter\Http\Response
	 */
	public function editGroup(int $id = 0)
	{
		// bail if no group id given
		if (! $this->isAuthorized() || ! $id)
		{
			return redirect()->to('/admin/users');
		}

		$validation = \Config\Services::validation();

		$data['title'] = lang('Auth.edit_group_title');

		$group = $this->ionAuth->group($id)->row();

		// validate form input
		$validation->setRule('group_name', lang('Auth.edit_group_validation_name_label'), 'required|alpha_dash');

		if ($this->request->getPost())
		{
			if ($validation->withRequest($this->request)->run())
			{
				$groupUpdate = $this->ionAuth->updateGroup($id, $this->request->getPost('group_name'), ['description' => $this->request->getPost('group_description')]);

				if ($groupUpdate)
				{
					session()->setFlashdata('message', lang('Auth.edit_group_saved'));
				}
				else
				{
					session()->setFlashdata('message', $this->ionAuth->errors());
				}
				return redirect()->to('/admin/users');
			}
		}

		helper(['form']);

		// set the flash data error message if there is one
		$data['message'] = $validation->listErrors() ?: ($this->ionAuth->errors() ?: session()->getFlashdata('message'));

		// pass the user to the view
		$data['group'] = $group;

		$readonly = $this->configIonAuth->adminGroup === $group->name ? 'readonly' : '';

		$data['group_name']        = [
			'name'    => 'group_name',
			'id'      => 'group_name',
			'type'    => 'text',
			'value'   => set_value('group_name', $group->name),
			$readonly => $readonly,
		];
		$data['group_description'] = [
			'name'  => 'group_description',
			'id'    => 'group_description',
			'type'  => 'text',
			'value' => set_value('group_description', $group->description),
		];

		$body = view('Admin\users\edit_group', $data);
		return $this->view($body, lang('Auth.edit_group_title'), 'users');
	}

	/**
	 * Create a new group
	 *
	 * @return string string|\CodeIgniter\HTTP\RedirectResponse
	 */
	public function createGroup()
	{
		if (! $this->isAuthorized())
		{
			return redirect()->to('/auth');
		}

		$data['title'] = lang('Auth.create_group_title');

		$validation = \Config\Services::validation();

		// validate form input
		$validation->setRule('group_name', lang('Auth.create_group_validation_name_label'), 'trim|required|alpha_dash');

		if ($this->request->getPost() && $validation->withRequest($this->request)->run())
		{
			$newGroupId = $this->ionAuth->createGroup($this->request->getPost('group_name'), $this->request->getPost('description'));
			if ($newGroupId)
			{
				// check to see if we are creating the group
				// redirect them back to the admin page
				session()->setFlashdata('message', $this->ionAuth->messages());
				return redirect()->to('/admin/users');
			}
		}
		else
		{
			// display the create group form
			helper(['form']);
			// set the flash data error message if there is one
			$data['message'] = $validation->getErrors() ? $validation->listErrors() : ($this->ionAuth->errors() ? $this->ionAuth->errors() : session()->getFlashdata('message'));

			$data['group_name'] = [
				'name'  => 'group_name',
				'id'    => 'group_name',
				'type'  => 'text',
				'value' => set_value('group_name'),
			];
			$data['description'] = [
				'name'  => 'description',
				'id'    => 'description',
				'type'  => 'text',
				'value' => set_value('description'),
			];

			$body = view('Admin\users\create_group', $data);
			return $this->view($body, lang('Auth.create_group_title'), 'users');
		}
	}
}
