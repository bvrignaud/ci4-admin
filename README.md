# ci-admin
Admin module for CodeIgniter 4

## Installing ci-admin

Before installing, please check that you are meeting the minimum server requirements.

There are different ways to install this package.


> 1. With composer

```shell
$ composer require https://github.com/bvrignaud/ci-admin
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
	$routes->get('informations', 'Informations::index');
});
```
