<?php


class tad_DI52_Extensions_PHPStormMetaGeneratingContainer extends \tad_DI52_Container {

	/**
	 * @var array
	 */
	protected $made = array();

	public function make( $classOrInterface ) {
		$made = parent::make( $classOrInterface );

		$this->registerMade( $classOrInterface, $made );

		return $made;
	}

	/**
	 * The file where
	 * @var string
	 */
	protected $destinatonFolder;

	public function __construct( $destinationFolder ) {
		parent::__construct();
		if ( ! is_dir( $destinationFolder ) ) {
			throw new InvalidArgumentException( "Destination folder '{$destinationFolder}' is not a folder." );
		}
		$this->destinatonFolder = rtrim( $destinationFolder, DIRECTORY_SEPARATOR );
	}

	function __destruct() {
		$this->printPhpStormMeta();
	}

	public function offsetGet( $offset ) {
		$made = parent::offsetGet( $offset );

		$this->registerMade( $offset, $made );

		return $made;
	}

	/**
	 * @param $classOrInterface
	 * @param $made
	 */
	protected function registerMade( $classOrInterface, $made ) {
		if ( class_exists( $classOrInterface ) ) {
			$classOrInterfaceReflection = new ReflectionClass( $classOrInterface );
			$classOrInterfaceClass = $classOrInterfaceReflection->getName();
		} else {
			$classOrInterfaceClass = $classOrInterface;
		}

		$madeClass = @get_class( $made );

		if ( false === $madeClass ) {
			return;
		}

		$implementationReflection = new ReflectionClass( $made );
		$implementationClass = $implementationReflection->getName();

		$classOrInterfaceClass = '\\' . ltrim( $classOrInterfaceClass, '\\' );
		$implementationClass = '\\' . ltrim( $implementationClass, '\\' );

		$this->made[ $classOrInterfaceClass ] = $implementationClass;
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

		foreach ( $this->made as $key => $implementation ) {
			$bindings[] = "{$key}::class => {$implementation}::class";
		}

		$contents = str_replace( '{{bindings}}', implode( ",\n\t\t\t", $bindings ), $template );

		file_put_contents( $destination, $contents );
	}
}
