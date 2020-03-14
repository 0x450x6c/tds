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
 * @template T
 *
 * @psalm-immutable
 */
interface Maybe
{
	/**
	 * The maybe function takes a default value, a function, and a Maybe value.
	 *
	 * If the Maybe value is Nothing, the function returns the default value.
	 *
	 * Otherwise, it applies the function
	 *    to the value inside the Just and returns the result.
	 *
	 * @template X
	 * @template Y
	 *
	 * @psalm-param X $defaultValue
	 * @phpstan-param X $defaultValue
	 * @phan-param X $defaultValue
	 *
	 * @psalm-param \Closure(T=):Y $predicate
	 * @phpstan-param \Closure(T):Y $predicate
	 * @phan-param \Closure(T):Y|\Closure():Y $predicate
	 *
	 * @psalm-return X|Y
	 * @phpstan-return X|Y
	 * @phan-return X|Y
	 *
	 * @psalm-pure
	 *
	 * @param mixed $defaultValue
	 */
	public function maybe($defaultValue, \Closure $predicate);

	/**
	 * @psalm-pure
	 */
	public function isJust(): bool;

	/**
	 * @psalm-pure
	 */
	public function isNothing(): bool;

	/**
	 * @psalm-return T
	 * @phpstan-return T
	 * @phan-return T
	 *
	 * @psalm-pure
	 *
	 * @throws FromJustNothingException
	 */
	public function fromJust();

	/**
	 * @template X
	 *
	 * @psalm-param X $defaultValue
	 * @phpstan-param X $defaultValue
	 * @phan-param X $defaultValue
	 *
	 * @psalm-return T|X
	 * @phpstan-return T|X
	 * @phan-return T|X
	 *
	 * @psalm-pure
	 *
	 * @param mixed $defaultValue
	 */
	public function fromMaybe($defaultValue);

	/**
	 * @psalm-return Listt<int, T>
	 * @phpstan-return Listt<int, T>
	 * @phan-return Listt<int, T>
	 */
	public function toList(): Listt;
}