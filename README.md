# LabbyBundle

[![Join the chat at https://gitter.im/matejvelikonja/LabbyBundle](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/matejvelikonja/LabbyBundle?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/matejvelikonja/LabbyBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/matejvelikonja/LabbyBundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/matejvelikonja/LabbyBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/matejvelikonja/LabbyBundle/?branch=master)
[![Build Status](https://travis-ci.org/matejvelikonja/LabbyBundle.svg?branch=master)](https://travis-ci.org/matejvelikonja/LabbyBundle)

LabbyBundle is Symfony2 bundle for retrieving database and assets from one stage to another.

**Warning** Bundle is in early stage of development.

## Installation

**Prerequisites**

* SSH connection to the remote server.

**Add LabbyBundle by running this command**

```bash
$ composer.phar require velikonja/labby-bundle "@stable"
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
#  process_timeout: 300    # Timeout for each external process run (import, dump, ssh, scp, ...).
#  roles: [ remote, local ]

  remote:
    hostname: example.com  # Server where the remote is hosted. 
    path:     /var/www/app # Path to application on remote.
#   env:      prod         # SF env to be run on remote (default true)

  fs:
#   timeout: 60            # Number of seconds in which one mapping (not all of them) sync timeouts.
    maps:                  # You can define more different mappings
      uploads: 
        src: example.com:/var/www/uploads/ # Mind the trailing slash
        dst: web/uploads/
      data:
        src: example.com:/var/www/data/
        dst: app/data/
        
# db:
#   recreate:             true # By default this value is true

# Following options are automatically fetched from doctrine.dbal configuration.
#   driver:               ~
#   dbname:               ~
#   host:                 ~
#   port:                 ~
#   user:                 ~
#   password:             ~
#   charset:              ~

# Run commands (symfony or shell) on certain events.
#  event_executors:
#    pre_sync:
#      - shell: "whoami"
#    post_sync:
#      - sf: "cache:clear"
#    post_sync.db:
#      - sf: "fos:user:change-password admin admin"
```

**Use the command to sync**

**Warning**: *Before you can first sync with remote, you have to deploy the code and configuration to remote.*

Sync assets and database:
```bash
$ app/console labby:sync
```

Sync only DB:
```bash
$ app/console labby:sync:db
```
Sync only assets:
```bash
$ app/console labby:sync:fs
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

*Check `.travis.yml` for more information about setting up test environment.*

**Extra**

```bash
$ bin/phpmd . text cleancode, codesize, controversial, design, naming, unusedcode --exclude vendor/
$ bin/phpcpd . --exclude=vendor
```
