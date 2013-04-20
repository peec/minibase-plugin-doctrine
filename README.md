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
		'entityDirs' => __DIR__ . '/Models', // Entity dirs.
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

```php
$this->mb->em->persist(new SomeModel());
```


