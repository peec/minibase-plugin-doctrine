# Doctrine ORM for Minibase

Enables you to integrate Doctrine to Minibase.

## Install

```json
{
  "require":{
	     "pkj/minibase-plugin-doctrine": "dev-master"
	}
}

```

## Setup

Init the plugin

```php
$mb->initPlugins(array(
	'Pkj\Minibase\Plugin\DoctrinePlugin\DoctrinePlugin' => array(
		'metadata' => 'annotation', // yaml,xml or annotation.
		'entityDirs' => [__DIR__ . '/Models'], // Entity dirs.
		'proxyDir' => __DIR__ . '/cache/proxies', // Cached proxies
		'connection' => array(
			'driver' => 'pdo_sqlite',
    			'path' => __DIR__ . '/db.sqlite',
		),
		// Optional callback to configure the Configuration object.
		setupCallback: function () {
			// $this = Doctrine Configuration object instance.
		}
	)
));
```


## Use in controllers

The plugin makes "em" available as a plugin in your MB app.


From any controller:


## Using the doctrine CLI

See the [Minibase CLI](https://github.com/peec/minibase/blob/master/docs/command-line.md) documentation on how you generate a php file that can run commands.

This plugin injects All Doctrine Cli commands to the default minibase commands.



```php
$this->mb->em->persist(new SomeModel());
```

## Events

#### plugin:doctrine:entityDirs (array &$entityDirs)

Listen to this event to add more entity dirs. Useful for other plugins that requires this plugin. Note that `$entityDirs` is a reference.

#### plugin:doctrine:setup (Doctrine\ORM\Configuration $setup)

Listen to this event to configure the configuration for doctrine before entity manager is created.




## Migrations

The best way to keep in track with production database is migrations.

Create `migrations.xml` where your `cli.php` binary is.

Add this (configure it yourself):

```xml
<?xml version="1.0" encoding="UTF-8"?>
<doctrine-migrations xmlns="http://doctrine-project.org/schemas/migrations/configuration"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://doctrine-project.org/schemas/migrations/configuration
                    http://doctrine-project.org/schemas/migrations/configuration.xsd">

    <name>App migrations</name>

    <migrations-namespace>app\migrations</migrations-namespace>

    <table name="doctrine_migration_versions" />

    <migrations-directory>app/migrations</migrations-directory>

</doctrine-migrations>
```

Create your first diff:

```bash
php cli.php migration:diff
```

Excecute migrations:

```bash
php cli.php migrations:migrate
```
