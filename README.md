# Twig Plugin for Minibase

Enables Twig based templates. Uses the same interface as Minibase offers for 
rendering normal PHP views.


## Install

```json
{
  "require":{
	     "pkj/minibase-plugin-twig": "dev-master"
	}
}

```

## Setup

Setup global view path, we need to know where twig templates are located.

```php
$mb->setConfig(MB::CFG_VIEWPATH, __DIR__ . '/views');
```

Init the plugin

```php
$mb->initPlugins(array(
	'Pkj\Minibase\Plugin\TwigPlugin\TwigPlugin' => array(
		// Where to store the compiled php templates.		
		'cache' => __DIR__ . '/template_compilation_cache'
	)
));
```

Start using twig templates.

