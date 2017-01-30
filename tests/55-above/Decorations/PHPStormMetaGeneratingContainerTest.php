<?php

class PHPStormMetaGeneratingContainerTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var string
	 */
	protected $destinationFolder;

	/**
	 * @var string
	 */
	protected $destinationFile;

	protected function tearDown()
	{
		unlink($this->destinationFolder . '/.phpstorm.meta.php');
	}

	public function interfacesAndImplementations()
	{
		return [
			One::class => ClassOne::class,
			Two::class => ClassTwo::class,
			ClassOne::class => ExtendingClassOneOne::class,
			Acme\One::class => Acme\ClassOne::class,
			Acme\Two::class => Acme\ClassTwo::class,
			Acme\ClassOne::class => Acme\ExtendingClassOneOne::class,
			'foo' => FiveBase::class,
			'foo.bar' => Acme\Three::class,
		];
	}

	/**
	 * interface make generation
	 */
	public function test_interface_make_generation()
	{

		$sut = $this->makeInstance();

		foreach ($this->interfacesAndImplementations() as $interface => $implementation) {
			$sut->bind($interface, $implementation);
			$sut->make($interface);
		}

		$decorators = array(
			FourDecoratorThree::class,
			FourDecoratorTwo::class,
			FourDecoratorOne::class,
			FourBase::class
		);
		$sut->bindDecorators(Four::class, $decorators);
		$sut->make(Four::class);

		$decorators = array(
			Acme\FourDecoratorThree::class,
			Acme\FourDecoratorTwo::class,
			Acme\FourDecoratorOne::class,
			Acme\FourBase::class
		);
		$sut->bindDecorators(Acme\Four::class, $decorators);
		$sut->make(Acme\Four::class);

		$sut->printPhpStormMeta();

		$this->assertFileExists($this->destinationFile);

		include_once $this->destinationFile;

		$map = PHPSTORM_META\tad_DI52_Container_Map();

		foreach ($this->interfacesAndImplementations() as $interface => $implementation) {
			$this->assertArrayHasKey($interface, $map);
			$this->assertEquals($implementation, $map[$interface]);
		}

		$this->assertArrayHasKey(Four::class, $map);
		$this->assertEquals(FourDecoratorThree::class, $map[Four::class]);
		$this->assertArrayHasKey(Acme\Four::class, $map);
		$this->assertEquals(Acme\FourDecoratorThree::class, $map[Acme\Four::class]);
	}

	protected function setUp()
	{
		$this->destinationFolder = dirname(dirname(dirname(__FILE__))) . '/data';
		$this->destinationFile = $this->destinationFolder . '/.phpstorm.meta.php';
	}

	/**
	 * @return tad_DI52_Decorations_PHPStormMetaGeneratingContainer
	 */
	protected function makeInstance()
	{
		return new tad_DI52_Decorations_PHPStormMetaGeneratingContainer(new tad_DI52_Container(),
			$this->destinationFolder);
	}
}
