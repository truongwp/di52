<?php


class tad_DI52_Decorations_PHPStormMetaGeneratingContainer extends tad_DI52_Decorations_Decorator {
	/**
	 * @var array
	 */
	protected $made = array();

	/**
	 * @var string
	 */
	protected $destinatonFolder;

	/**
	 * tad_DI52_Decorations_PHPStormMetaGeneratingContainer constructor.
	 * @param tad_DI52_ContainerInterface $container
	 * @param $destinationFolder
	 */
	public function __construct(tad_DI52_ContainerInterface $container, $destinationFolder) {
		parent::__construct($container);
		if (!is_dir($destinationFolder)) {
			throw new InvalidArgumentException("Destination folder '{$destinationFolder}' is not a folder.");
		}
		$this->destinatonFolder = rtrim($destinationFolder, DIRECTORY_SEPARATOR);
	}

	function __destruct() {
		$this->printPhpStormMeta();
	}

	public function printPhpStormMeta() {
		$destination = $this->destinatonFolder . '/.phpstorm.meta.php';

		$template = <<<PHP
<?php

namespace PHPSTORM_META {
	
	// for testing purposes
	function tad_DI52_Container_Map() {
		return array(
			{{bindings}}
		);
	}

	if ( function_exists( '\PHPSTORM_META\override' ) ) {
		override(\\tad_DI52_Container::make(0), map([
			{{bindings}}
		]));
		// support ArrayAccess ['key'] like calls
		override(new \\tad_DI52_Container, map([
			{{bindings}}
		]));
	}
}
PHP;

		$bindings = array();

		foreach ($this->made as $key => $implementation) {
			$bindings[] = "{$key} => {$implementation}::class";
		}

		$contents = str_replace('{{bindings}}', implode(",\n\t\t\t", $bindings), $template);

		file_put_contents($destination, $contents);
	}

	public function make($classOrInterface) {
		$made = $this->container->make($classOrInterface);

		$this->registerMade($classOrInterface, $made);

		return $made;
	}

	/**
	 * @param $classOrInterface
	 * @param $made
	 */
	protected function registerMade($classOrInterface, $made) {
		if (class_exists($classOrInterface) || interface_exists($classOrInterface) || trait_exists($classOrInterface)) {
			$classOrInterfaceReflection = new ReflectionClass($classOrInterface);
			$classOrInterfaceClass = $classOrInterfaceReflection->getName();
			$classOrInterfaceClass = '\\' . ltrim($classOrInterfaceClass, '\\') . '::class';
		} else {
			$classOrInterfaceClass = "'{$classOrInterface}'";
		}

		$madeClass = @get_class($made);

		if (false === $madeClass) {
			return;
		}

		$implementationReflection = new ReflectionClass($made);
		$implementationClass = $implementationReflection->getName();

		$implementationClass = '\\' . ltrim($implementationClass, '\\');

		$this->made[$classOrInterfaceClass] = $implementationClass;
	}

	public function offsetGet($offset) {
		$made = $this->container->offsetGet($offset);

		$this->registerMade($offset, $made);

		return $made;
	}
}
