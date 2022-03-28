##Movie API


Requirements
------------

* PHP 7.2.5;
* and the [usual Symfony application requirements][1].

Installation
------------

```bash
$ composer install
```



Download pdAdmin

composer create-project appaydin/pd-admin pdadmin

Create and configure the .env file.

Create database schemas

```bash
$ bin/console doctrine:schema:create --force
```

Utilisez cette commande pour Asyn Emailing
```bash
$ php bin/console messenger:consume async -vv
```

Run built-in web server

symfony server:start --no-tls -d







[1]: https://symfony.com/doc/current/setup.html#technical-requirements
