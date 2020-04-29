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
 * @phpstan-template T
 * @phan-template T
 *
 * @template-implements \Iterator<int, T>
 *
 * @psalm-immutable
 */
abstract class Maybe implements \Iterator, \Countable, \Serializable
{
	/**
	 * Alias for Maybe::apply().
	 *
	 * @psalm-param \Closure(T) $predicate
	 * @phpstan-param \Closure(T):(void|mixed) $predicate
	 * @phan-param \Closure(T):(void|mixed) $predicate
	 *
	 * @psalm-pure
	 */
	abstract public function __invoke(\Closure $predicate): void;

	/**
	 * The maybe function takes a default value, a function, and a Maybe value.
	 *
	 * If the Maybe value is Nothing, the function returns the default value.
	 *
	 * Otherwise, it applies the function
	 *    to the value inside the Just and returns the result.
	 *
	 * @psalm-template X
	 * @phpstan-template X
	 * @phan-template X
	 *
	 * @psalm-template Y
	 * @phpstan-template Y
	 * @phan-template Y
	 *
	 * @psalm-param X $defaultValue
	 * @phpstan-param X $defaultValue
	 * @phan-param X $defaultValue
	 *
	 * @psalm-param \Closure(T):Y $predicate
	 * @phpstan-param \Closure(T):Y $predicate
	 * @phan-param \Closure(T):Y $predicate
	 *
	 * @psalm-return X|Y
	 * @phpstan-return X|Y
	 * @phan-return X|Y
	 *
	 * @psalm-pure
	 *
	 * @param mixed $defaultValue
	 */
	abstract public function maybe($defaultValue, \Closure $predicate);

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
	 * @phpstan-return T
	 * @phan-return T
	 *
	 * @psalm-pure
	 *
	 * @throws UsingFromJustOnNothingException
	 */
	abstract public function fromJust();

	/**
	 * @psalm-template X
	 * @phpstan-template X
	 * @phan-template X
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
	abstract public function fromMaybe($defaultValue);

	/**
	 * @psalm-return Listt<int, T>
	 * @phpstan-return Listt<int, T>
	 * @phan-return Listt<int, T>
	 *
	 * @psalm-pure
	 */
	abstract public function toList(): Listt;

	/**
	 * Apply predicate if `Just`.
	 *
	 * @psalm-param \Closure(T) $predicate
	 * @phpstan-param \Closure(T):(void|mixed) $predicate
	 * @phan-param \Closure(T):(void|mixed) $predicate
	 *
	 * @psalm-pure
	 */
	abstract public function apply(\Closure $predicate): void;

	abstract public function key(): int;
}
