<?php namespace Admin\Controllers;

/**
 * Admin/Information controller file
 *
 * @package CI-Admin
 * @author  Benoit VRIGNAUD <benoit.vrignaud@zaclys.net>
 * @license https://opensource.org/licenses/MIT	MIT License
 * @link    http://github.com/bbvrignaud/ci-admin
 */

class Informations extends AbstractAdminController
{
	use \CodeIgniter\API\ResponseTrait;

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

		helper('form');

		$data = [
			'dbVersion' => \Config\Database::connect()->getVersion(),
			'ciVersion' => \CodeIgniter\CodeIgniter::CI_VERSION,
		];
		$body = view ('Admin\informations', $data);

		return $this->view($body, lang('Admin.informations-title'), 'informations');
	}

	/**
	 * Display phpinfo
	 *
	 * @return \CodeIgniter\HTTP\RedirectResponse|string
	 */
	public function displayPhpInfo()
	{
		if (! $this->isAuthorized())
		{
			return redirect()->to('/');
		}

		return phpinfo();
	}

	/**
	 * Download database
	 *
	 * @return \CodeIgniter\HTTP\RedirectResponse|string
	 */
	public function exportDatabase()
	{
		if (! $this->isAuthorized())
		{
			return redirect()->to('/');
		}

		$dbUtil = \Config\Database::utils();
		$backup = $dbUtil->backup(['format' => 'gzip']);

		return $this->response->download(date('Y-m-d') . '-backup.gz', $backup);

		/*
		// Load the DB utility class
		$this->load->dbutil();

		// Backup your entire database and assign it to a variable
		$backup = $this->dbutil->backup(['format' => 'gzip']);

		// Load the download helper and send the file to your desktop
		$this->load->helper('download');
		force_download(date('Y-m-d') . '-backup.gz', $backup);
		*/
	}

	/**
	 * Test si l'envoi d'e-mail est correctement configuré
	 */
	public function sendEmailForTest()
	{

		if (isset($_POST['email']))
		{
			$email = \Config\Services::email();
			//$email->setFrom($this->config->item('contact_email'));
			$email->setFrom('test@free.fr');
			$email->setTo($this->request->getPost('email'));
			$email->setSubject('Test');
			$email->setMessage('Test d\'envoi de mail.');

			/*
			if ($this->email->send()) {
				$this->sendAjaxJSONReponse(true, 'Email envoyé avec succès. Contrôler votre boite mail.');
			} else {
				$reponse = [
					'success' => false,
					'msg' => 'Erreur durant l\'envoi du mail !',
					'logs' => $this->email->print_debugger(),
				];
				echo json_encode($reponse);
			}
			*/
			if ($email->send())
			{
				return $this->respondCreated([]);
			}
			else
			{
				return $this->fail('Une erreur est survenue durant l\'envoi du mail');
			}
		}
	}
}
