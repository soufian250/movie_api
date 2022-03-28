##Movie API

L'environnement de travail est **Symfony**

Requirements
------------

* PHP 7.2.5;
* Composer 2.2.9
* MySql 8


Installation
------------

```bash
$ composer install
```


####- Configurer le fichier .env 
`DATABASE_URL`
`MAILER_DSN`


Crée la base de donnée
```bash
$ bin/console doctrine:database:create
```


Création des tables/schémas de base de données
```bash
$ bin/console make:migration
```

exécuter la migration
Creating the Database Tables/Schema
```bash
$ bin/console doctrine:migrations:migrate
```

Démarer le serveur
```bash
$ symfony server:start
```


Utilisez cette commande pour activer l'envoi des mails en mode Async
```bash
$ php bin/console messenger:consume async -vv
```
