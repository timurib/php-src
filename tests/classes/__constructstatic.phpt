--TEST--
Static constructor test
--FILE--
<?php
/*** Basic case ***/
class Simple {
	public static $static_property = false;

	public static function __constructStatic() {
		self::$static_property = true;
	}
}

var_dump(Simple::$static_property);

/*** Catch exceptions ***/
try {
	class ErrorCausing {
		public static function __constructStatic() {
			throw new Exception('Error from static ctor');
		}
	}
} catch (Exception $e) {
	echo $e->getMessage(), "\n";
}

/*** Complex case: inheritance and late static binding ***/
class Base {
	public static $foo = 1;
	public static $bar = 2;

	public static function __constructStatic() {
		self::$foo = static::$bar;
	}
}

var_dump(Base::$foo);

class ExtendedOne extends Base {
	public static $bar = 3;
}

var_dump(Base::$foo);

class ExtendedTwo extends Base {
    public static $bar = 3;
    public static function __constructStatic() {
        parent::__constructStatic();
    }
}

var_dump(Base::$foo);

/*** Invalid declaration ***/
class WrongVisibility
{
    private static function __constructStatic() {}
}
?>
--EXPECTF--
Warning: The magic method __callStatic() must have public visibility and be static in %s__constructstatic.php on line %d
bool(true)
Error from static ctor
int(2)
int(2)
int(3)

Fatal error: Uncaught Error: Call to private method WrongVisibility::__constructStatic() from context '' in %s__constructstatic.php:%d
Stack trace:
#0 {main}
  thrown in %s__constructstatic.php on line %d
