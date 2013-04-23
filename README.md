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

