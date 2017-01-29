<?php

class PHPStormMetaGeneratingContainerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var string
	 */
	protected $destinationFolder;

	/**
	 * @var string
	 */
	protected $destinationFile;

	protected function tearDown() {
		unlink( $this->destinationFolder . '/.phpstorm.meta.php' );
	}

	/**
	 * interface make generation
	 */
	public function test_interface_make_generation() {
		$sut = $this->makeInstance();

		$sut->bind( One::class, ClassOne::class );
		$sut->make( One::class );

		$sut->printPhpStormMeta();

		$this->assertFileExists( $this->destinationFile );

		include_once $this->destinationFile;

		$map = PHPSTORM_META\tad_DI52_Container_Map();

		$this->assertArrayHasKey( One::class, $map );
		$this->assertEquals( ClassOne::class, $map[ One::class ] );
	}

	protected function setUp() {
		$this->destinationFolder = dirname( dirname( dirname( __FILE__ ) ) ) . '/data';
		$this->destinationFile = $this->destinationFolder . '/.phpstorm.meta.php';
	}

	/**
	 * @return tad_DI52_Extensions_PHPStormMetaGeneratingContainer
	 */
	protected function makeInstance() {
		return new tad_DI52_Extensions_PHPStormMetaGeneratingContainer( $this->destinationFolder );
	}
}
