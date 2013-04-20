<?php
namespace Pkj\Minibase\Plugin\DoctrinePlugin;

use Symfony\Component\Console\Application;

use Minibase\Plugin\Plugin;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class DoctrinePlugin extends Plugin{
	
	public $cliListener;
	
	public function setup () {

		$metaConfig = $this->cfg('metadata', 'annotation');
		$entityDirs = $this->cfg('entityDirs');
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
		
		$callback = $this->cfg('setupCallback');
		if ($callback) {
			$callback = $callback->bindTo($setup);
			$callback();
		}
		
		$this->mb->plugin('em', function () use ($conn, $setup) {
			
			return EntityManager::create($conn, $setup);
		});
		
		
		
		$this->cliListener = function (Application $console) {			
			$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
				'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($this->mb->em->getConnection()),
				'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($this->mb->em)
			));
			$console->setHelperSet($helperSet);
		};
	}
	
	public function start () {
		$this->mb->events->on("mb:console", $this->cliListener, $this);
	}
	
	public function stop () {
		$this->mb->events->off("mb:console", $this->cliListener);
	}
	
	
}