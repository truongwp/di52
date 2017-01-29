<?php


class tad_DI52_PHPStormMetaGeneratingContainer extends \tad_DI52_Container {

	/**
	 * @var array
	 */
	protected $made = array();

	public function make( $classOrInterface ) {
		$made = parent::make( $classOrInterface );

		if ( class_exists( $classOrInterface ) ) {
			$classOrInterfaceReflection = new ReflectionClass( $classOrInterface );
			$classOrInterfaceClass = $classOrInterfaceReflection->getName();
		} else {
			$classOrInterfaceClass = $classOrInterface;
		}

		$implementationReflection = new ReflectionClass( $made );
		$implementationClass = $implementationReflection->getName();

		$classOrInterfaceClass = '\\' . ltrim( $classOrInterfaceClass, '\\' );

		$this->made[ $classOrInterfaceClass ] = $implementationClass;

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
		$destination = $this->destinatonFolder . '/.phpstorm.meta.php';

		$template = <<<PHP
<?php

namespace PHPSTORM_META {
	\$STATIC_METHOD_TYPES = [
		\\tad_DI52_Container::make( '' ) => [
			{{bindings}}
		]
	];
}

PHP;

		$bindings = array();

		foreach ( $this->made as $key => $implementation ) {
			$bindings[] = "{$key} instanceof {$implementation}";
		}

		$contents = str_replace( '{{bindings}}', implode( ",\n\t\t\t", $bindings ), $template );

		file_put_contents( $destination, $contents );
	}
}