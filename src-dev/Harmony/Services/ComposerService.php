<?php 
namespace Harmony\Services;

use Composer\IO\IOInterface;
use Mouf\Composer\MoufErrorLogComposerIO;
use Composer\Factory;

/**
 * A utility class to fetch the Composer object from the path to a composer.json file.
 * 
 * @author David Négrier
 */
class ComposerService {

	/**
	 * @var string
	 */
	protected $composerJsonPath;

	/**
	 * @var Composer
	 */
	protected $composer;
	
	/**
	 * @var IOInterface
	 */
	protected $io;
	
	public function __construct($composerJsonPath) {
		$this->composerJsonPath = $composerJsonPath;
	}

	private function configureEnv() {
		if (dirname($this->composerJsonPath)) {
			chdir(dirname($this->composerJsonPath));
		}
		\putenv('COMPOSER='.basename($this->composerJsonPath));
	}

	/**
	 * Exposes the Composer object
	 * 
	 * @return Composer 
	 */
	public function getComposer() {
		if (null === $this->composer) {
			
			$this->configureEnv();
			
			$this->io = new MoufErrorLogComposerIO();
			$this->composer = Factory::create($this->io);
		}
		return $this->composer;
	}

}
