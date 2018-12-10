# ci4-admin
Admin module for CodeIgniter 4 based on AdminLTE 3

## Installing ci-admin

Before installing, please check that you are meeting the minimum server requirements.

There are different ways to install this package.


> 1. With composer

```shell
$ composer require bvrignaud/ci4-admin
```
---

> 2. With Git:

```shell
my-project$ git clone https://github.com/bvrignaud/ci-admin.git
```
Then in your Config/Autoload.php, add this :
```php
'Admin' => ROOTPATH . 'ci-admin',
```

---

> 3. Download the archive, and move folder from this package to the root folder:

```shell
CI                          # → Root Directory
├── application/
├── ion-auth/               # → Ion-auth directory
├── public
├──...
```
Then in your Config/Autoload.php, add this :
```php
'IonAuth' => ROOTPATH . 'YOUR-ION_AUTH-FOLDER',
```

---
Install css/js dependencies
```bash
$ cd public/assets
$ yarn add admin-lte@v3
```
---

## Use it

Add routes configs in 'Config\Routes.php':
```php
$routes->group('auth', ['namespace' => 'IonAuth\Controllers'], function ($routes) {
	$routes->get('/', 'Auth::index');
	$routes->add('login', 'Auth::login');
	$routes->get('logout', 'Auth::logout');
	$routes->get('forgot_password', 'Auth::forgot_password');
});

$routes->group('admin', ['namespace' => 'Admin\Controllers'], function ($routes) {
	$routes->get('/', 'Home::index');

	$routes->group('users', ['namespace' => 'Admin\Controllers'], function ($routes) {
		$routes->get('/', 'Users::index');
		$routes->add('create', 'Users::createUser');
		$routes->add('edit/(:num)', 'Users::edit/$1');
		$routes->add('activate/(:num)', 'Users::activate/$1');
		$routes->add('deactivate/(:num)', 'Users::deactivate/$1');
		$routes->add('edit_group/(:num)', 'Users::editGroup/$1');
		$routes->add('create_group', 'Users::createGroup');
	});

	$routes->group('informations', ['namespace' => 'Admin\Controllers'], function ($routes) {
		$routes->get('/', 'Informations::index');
		$routes->get('displayPhpInfo', 'Informations::displayPhpInfo');
		$routes->add('exportDatabase', 'Informations::exportDatabase');
		$routes->post('sendEmailForTest', 'Informations::sendEmailForTest');
	});
});
```
