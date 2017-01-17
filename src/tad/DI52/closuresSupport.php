<?php
/**
 * Builds and returns a closure to be used to build to lazily make objects on PHP 5.3+.
 *
 * @param tad_DI52_Container $container
 * @param string $classOrInterface
 * @param string $method
 *
 * @return Closure
 */
function di52_callbackClosure(tad_DI52_Container $container, $classOrInterface, $method) {
	return function () use ($container, $classOrInterface, $method) {
		$a = func_get_args();
		$i = $container->make($classOrInterface);
		return call_user_func_array(array($i, $method), $a);
	};
}

function di52_instanceClosure(tad_DI52_Container$container,$classOrInterface,array $vars=array()){
    return function () use ($container, $classOrInterface, $vars) {
        $r = new ReflectionClass($classOrInterface);
        $constructor = $r->getConstructor();
        if (null === $constructor) {
            return new $classOrInterface;
        }
        $args = array();
        foreach ($vars as $var) {
            try {
                $args[] = $container->make($var);
            } catch (RuntimeException $e) {
                $args[] = $var;
            }
        }
        return $r->newInstanceArgs($args);
    };
}
