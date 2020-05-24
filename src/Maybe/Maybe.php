<?php declare(strict_types=1);

namespace TDS\Maybe;

use TDS\Listt\Listt;

/**
 * Clone of http://hackage.haskell.org/package/base-4.12.0.0/docs/Data-Maybe.html.
 *
 * The Maybe type encapsulates an optional value.
 * A value of type Maybe a either contains a value of type `a`
 *     (represented as `Just a`),
 *     or it is empty (represented as `Nothing`).
 *
 * Using Maybe is a good way to deal with errors or exceptional cases
 *     without resorting to drastic measures such as error.
 *
 * @psalm-template T
 *
 * @template-extends Listt<int, T>
 *
 * @psalm-immutable
 */
abstract class Maybe extends Listt
{
	/**
	 * The maybe function takes a default value, a function, and a Maybe value.
	 *
	 * If the Maybe value is Nothing, the function returns the default value.
	 *
	 * Otherwise, it applies the function
	 *    to the value inside the Just and returns the result.
	 *
	 * @psalm-template X
	 *
	 * @psalm-template Y
	 *
	 * @psalm-param X $defaultValue
	 *
	 * @psalm-param callable(T):Y $predicate
	 *
	 * @psalm-return X|Y
	 *
	 * @psalm-pure
	 *
	 * @param mixed $defaultValue
	 */
	abstract public function maybe($defaultValue, callable $predicate);

	/**
	 * @psalm-pure
	 */
	abstract public function isJust(): bool;

	/**
	 * @psalm-pure
	 */
	abstract public function isNothing(): bool;

	/**
	 * @psalm-return T
	 *
	 * @psalm-pure
	 *
	 * @throws UsingFromJustOnNothingException
	 */
	abstract public function fromJust();

	/**
	 * @psalm-template X
	 *
	 * @psalm-param X $defaultValue
	 *
	 * @psalm-return T|X
	 *
	 * @psalm-pure
	 *
	 * @param mixed $defaultValue
	 */
	abstract public function fromMaybe($defaultValue);

	/**
	 * @psalm-return Listt<int, T>
	 *
	 * @psalm-pure
	 */
	abstract public function toList(): Listt;
}
