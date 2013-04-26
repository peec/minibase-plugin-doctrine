<?php
namespace Pkj\Minibase\Plugin\DoctrinePlugin;

use Doctrine\ORM\Proxy\Autoloader;

use Symfony\Component\Console\Application;

use Minibase\Plugin\Plugin;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\AnnotationReader;


class DoctrinePlugin extends Plugin{
	
	public $cliListener;
	
	public function setup () {

		$metaConfig = $this->cfg('metadata', 'annotation');
		$entityDirs = $this->cfg('entityDirs');
		$proxyDir = $this->cfg('proxyDir');
		
		if (!$proxyDir){
			throw new \Exception ("Doctrine plugin requires 'proxyDir' to be set. Proxy dir is used for caching proxies.");
		}
		
		if (!$entityDirs) {
			throw new \Exception ("Doctrine plugin requires 'entityDirs' to be set. entityDirs must be an array of path(s) to where Models/Entities are located.");
		}
		
		if (!is_dir($proxyDir)) {
			mkdir($proxyDir);
		}
		
		
		$this->mb->events->trigger("plugin:doctrine:entityDirs", array(&$entityDirs));
		
		$conn = $this->cfg('connection');
		
		switch($metaConfig) {
			case "yaml":
				$setup = Setup::createYAMLMetadataConfiguration($entityDirs, $this->mb->isDevelopment());
				break;
			case "xml":
				$setup = Setup::createXMLMetadataConfiguration($entityDirs, $this->mb->isDevelopment());
				break;
			default:
				$setup = Setup::createAnnotationMetadataConfiguration($entityDirs, $this->mb->isDevelopment());
				break;
		}
		
		
		$setup->setMetadataCacheImpl($this->mb->cache);
		$setup->setQueryCacheImpl($this->mb->cache);	
		$setup->setMetadataDriverImpl(
				new AnnotationDriver(
						new CachedReader(
								new AnnotationReader(),
								$this->mb->cache
						),
						$entityDirs
				)
		);
		$setup->setProxyDir($proxyDir);
		
		$setup->setAutoGenerateProxyClasses($this->mb->isDevelopment());
		
		Autoloader::register($proxyDir, "DoctrineProxies");
		
		$callback = $this->cfg('setupCallback');
		if ($callback) {
			$callback = $callback->bindTo($setup);
			$callback();
		}
		
		$this->mb->events->trigger("plugin:doctrine:setup", array($setup));
		
		$this->mb->plugin('em', function () use ($conn, $setup) {
			
			return EntityManager::create($conn, $setup);
		});
		
		
		
		$this->cliListener = function (Application $console) {
			$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
				'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($this->mb->em->getConnection()),
				'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($this->mb->em),
				'dialog' => new \Symfony\Component\Console\Helper\DialogHelper()
			));
			$console->setHelperSet($helperSet);
			$console->addCommands(array(
					// DBAL Commands
					new \Doctrine\DBAL\Tools\Console\Command\RunSqlCommand(),
					new \Doctrine\DBAL\Tools\Console\Command\ImportCommand(),
					
					new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand(),
					new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand(),
					new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand(),
					new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand(),
					new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand(),
					new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand(),

					// ORM Commands
					new \Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand(),
					new \Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand(),
					new \Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand(),
					new \Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand(),
					new \Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand(),
					new \Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand(),
					new \Doctrine\ORM\Tools\Console\Command\EnsureProductionSettingsCommand(),
					new \Doctrine\ORM\Tools\Console\Command\ConvertDoctrine1SchemaCommand(),
					new \Doctrine\ORM\Tools\Console\Command\GenerateRepositoriesCommand(),
					new \Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand(),
					new \Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand(),
					new \Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand(),
					new \Doctrine\ORM\Tools\Console\Command\RunDqlCommand()
			));
			
			
		};
	}
	
	public function start () {
		$this->mb->events->on("mb:console", $this->cliListener, $this);
	}
	
	public function stop () {
		$this->mb->events->off("mb:console", $this->cliListener);
	}
	
	
}