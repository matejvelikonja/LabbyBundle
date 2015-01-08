# LabbyBundle

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/matejvelikonja/LabbyBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/matejvelikonja/LabbyBundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/matejvelikonja/LabbyBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/matejvelikonja/LabbyBundle/?branch=master)
[![Build Status](https://travis-ci.org/matejvelikonja/LabbyBundle.svg?branch=master)](https://travis-ci.org/matejvelikonja/LabbyBundle)

LabbyBundle is Symfony2 bundle for retrieving database and assets from one stage to another.

**Warning** Bundle is in early stage of development.

## Installation

**Add LabbyBundle by running this command**

```bash
$ composer.phar require velikonja/labby-bundle "dev-master"
```

**Enable the bundle in AppKernel**

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Velikonja\LabbyBundle\VelikonjaLabbyBundle(),
    );
}
```

**Configure the bundle**

```yml
velikonja_labby:
   password_reset: ~
#  By default it changes admin's password to admin.
#  password_reset:
#    users: [ {username: admin, password: admin}, {username: admin2, password: admin2} ]

#  roles: [ remote, local ]
  remote:
    hostname: example.com  # Server where the remote is hosted. 
    path:     /var/www/app # Path to application on remote.
#   env:      prod         # SF env to be run on remote
  fs:
    maps:
      uploads:
        src: example.com:/var/www/uploads/ # Mind the trailing slash
        dst: web/uploads/
```

## Contributing

**Install dependencies**

```bash
$ composer.phar install
```

**Run tests**

```bash
$ bin/phpunit
```

**Extra**

```bash
$ bin/phpmd . text cleancode, codesize, controversial, design, naming, unusedcode --exclude vendor/
$ bin/phpcpd . --exclude=vendor
```
