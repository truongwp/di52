<?php

namespace Acme;

interface One
{

}

interface Two
{

}

class ClassOne implements One
{

}

class ExtendingClassOneOne extends ClassOne
{
}

class ClassOneOne implements One
{
	public function __construct()
	{

	}
}

class ClassOneTwo implements One
{
	/**
	 * @var string
	 */
	private $foo;

	public function __construct($foo = 'bar')
	{

		$this->foo = $foo;
	}

	/**
	 * @return string
	 */
	public function getFoo()
	{
		return $this->foo;
	}
}

class ClassTwo implements Two
{
	/**
	 * @var One
	 */
	private $one;

	public function __construct(One $one)
	{

		$this->one = $one;
	}

	/**
	 * @return One
	 */
	public function getOne()
	{
		return $this->one;
	}
}

class ClassTen
{
	public static $builtTimes = 0;
	private $varOne;
	private $varTwo;
	private $varThree;

	public static function reset()
	{
		self::$builtTimes = 0;
	}

	public function __construct($varOne, $varTwo, $varThree)
	{
		self::$builtTimes++;
		$this->varOne = $varOne;
		$this->varTwo = $varTwo;
		$this->varThree = $varThree;
	}

	public function getVarOne()
	{
		return $this->varOne;
	}

	public function getVarTwo()
	{
		return $this->varTwo;
	}

	public function getVarThree()
	{
		return $this->varThree;
	}
}

class ClassEleven
{
	public static $builtTimes = 0;
	private $varOne;
	private $varTwo;
	private $varThree;

	public static function reset()
	{
		self::$builtTimes = 0;
	}

	public function __construct(One $varOne, ClassTwo $varTwo, $varThree)
	{
		self::$builtTimes++;
		$this->varOne = $varOne;
		$this->varTwo = $varTwo;
		$this->varThree = $varThree;
	}

	public function getVarOne()
	{
		return $this->varOne;
	}

	public function getVarTwo()
	{
		return $this->varTwo;
	}

	public function getVarThree()
	{
		return $this->varThree;
	}
}

class ClassTwelve
{
	public static $builtTimes = 0;
	private $varOne;

	public static function reset()
	{
		self::$builtTimes = 0;
	}

	public function __construct(One $varOne)
	{
		self::$builtTimes++;
		$this->varOne = $varOne;
	}

	public function getVarOne()
	{
		return $this->varOne;
	}
}

class Three
{
}

interface Four
{

}

class FourBase implements Four
{
	public function __construct()
	{

	}

	public function methodOne()
	{
		global $one;
		$one = __CLASS__;
	}

	public function methodTwo()
	{
		global $two;
		$two = __CLASS__;
	}

	public function methodThree($n)
	{
		return $n + 23;
	}
}

class FourTwo implements Four
{

}

class FourDecoratorOne implements Four
{
	public function __construct(Four $decorated)
	{

	}

	public function methodOne($n)
	{
		return $n + 23;
	}
}

class FourDecoratorTwo implements Four
{
	public function __construct(Four $decorated)
	{

	}
}

class FourDecoratorThree implements Four
{
	public function __construct(Four $decorated)
	{

	}
}
interface Five
{

}
class FiveBase implements Five
{
	public function __construct($foo = 10)
	{
	}
}

class FiveDecoratorOne implements Five
{
	public function __construct(Five $five, Four $four)
	{

	}
}

class FiveDecoratorTwo implements Five
{
	public function __construct(Five $five, One $one)
	{

	}
}

class FiveDecoratorThree implements Five
{
	public function __construct(Five $five, Two $two)
	{

	}
}
