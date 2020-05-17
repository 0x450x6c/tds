<?php declare(strict_types=1);

namespace TDS\Maybe;

use TDS\Listt\Listt;

/**
 * @psalm-pure
 */
function nothing(): Nothing
{
	return Nothing::instance();
}

/**
 * @psalm-template T
 * @phpstan-template T
 *
 * @psalm-param T $value
 * @phpstan-param T $value
 *
 * @psalm-return Just<T>
 * @phpstan-return Just<T>
 *
 * @psalm-pure
 *
 * @param mixed $value
 */
function just($value): Just
{
	return Just::new($value);
}

/**
 * The maybe function takes a default value, a function, and a Maybe value.
 *
 * If the Maybe value is Nothing, the function returns the default value.
 *
 * Otherwise, it applies the function
 *    to the value inside the Just and returns the result.
 *
 * @psalm-template T
 * @phpstan-template T
 *
 * @psalm-template X
 * @phpstan-template X
 *
 * @psalm-template Y
 * @phpstan-template Y
 *
 * @psalm-param Maybe<T> $maybe
 * @phpstan-param Maybe<T> $maybe
 *
 * @psalm-param X $defaultValue
 * @phpstan-param X $defaultValue
 *
 * @psalm-param callable(T):Y $predicate
 * @phpstan-param callable(T):Y $predicate
 *
 * @psalm-return X|Y
 * @phpstan-return X|Y
 *
 * @psalm-pure
 *
 * @param mixed $defaultValue
 */
function maybe(Maybe $maybe, $defaultValue, callable $predicate)
{
	return $maybe->maybe($defaultValue, $predicate);
}

/**
 * @psalm-template T
 * @phpstan-template T
 *
 * @param Maybe<T> $maybe
 * @psalm-assert-if-true Just<T> $maybe
 * @psalm-assert-if-false Nothing<T> $maybe
 *
 * @psalm-pure
 */
function isJust(Maybe $maybe): bool
{
	return $maybe->isJust();
}

/**
 * @psalm-template T
 * @phpstan-template T
 *
 * @param Maybe<T> $maybe
 * @psalm-assert-if-true Nothing<T> $maybe
 * @psalm-assert-if-false Just<T> $maybe
 *
 * @psalm-pure
 */
function isNothing(Maybe $maybe): bool
{
	return $maybe->isNothing();
}

/**
 * @psalm-template T
 * @phpstan-template T
 *
 * @psalm-param Maybe<T> $maybe
 * @phpstan-param Maybe<T> $maybe
 *
 * @psalm-return T
 * @phpstan-return T
 *
 * @psalm-pure
 *
 * @psalm-assert Just<T> $maybe
 */
function fromJust(Maybe $maybe)
{
	return $maybe->fromJust();
}

/**
 * @psalm-template X
 * @phpstan-template X
 *
 * @psalm-template T
 * @phpstan-template T
 *
 * @psalm-param Maybe<T> $maybe
 * @phpstan-param Maybe<T> $maybe
 *
 * @psalm-param X $defaultValue
 * @phpstan-param X $defaultValue
 *
 * @psalm-return T|X
 * @phpstan-return T|X
 *
 * @psalm-pure
 *
 * @param mixed $defaultValue
 */
function fromMaybe(Maybe $maybe, $defaultValue)
{
	return $maybe->fromMaybe($defaultValue);
}

/**
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-return Maybe<TValue>
 * @phpstan-return Maybe<TValue>
 *
 * @psalm-pure
 */
function listToMaybe(iterable $list): Maybe
{
	return Listt::fromIter($list)->toMaybe();
}

/**
 * @psalm-template T
 * @phpstan-template T
 *
 * @psalm-param Maybe<T> $maybe
 * @phpstan-param Maybe<T> $maybe
 *
 * @psalm-return Listt<int, T>
 * @phpstan-return Listt<int, T>
 */
function maybeToList(Maybe $maybe): Listt
{
	return $maybe->toList();
}

/**
 * The `catMaybes` function takes a list of `Maybes`
 *     and returns a list of all the `Just` values.
 *
 * This is lazy function,
 *     will be applied only when you are reading data from list.
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, Maybe<TValue>> $maybes
 * @phpstan-param iterable<TKey, Maybe<TValue>> $maybes
 *
 * @psalm-pure
 *
 * @psalm-return Listt<TKey, TValue>
 * @phpstan-return Listt<TKey, TValue>
 */
function catMaybes(iterable $maybes): Listt
{
	return Listt::fromIter($maybes)
		->select(static fn (Maybe $x): bool => $x instanceof Just)
		->map(static fn (Maybe $x) => $x->fromJust())
	;
}

/**
 * This is a version of map which can throw out elements.
 *
 * In particular, the functional argument returns something of type `Maybe b`.
 *
 * If this is Nothing, no element is added on to the result list.
 *
 * If it is `Just b`, then `b` is included in the result list.
 *
 * This is lazy function,
 *     will be applied only when you are reading data from list.
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-template X
 * @phpstan-template X
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-param callable(TValue=, TKey=):Maybe<X> $predicate
 * @phpstan-param callable(TValue, TKey|mixed):Maybe<X> $predicate
 *
 * @psalm-pure
 *
 * @psalm-return Listt<TKey, X>
 * @phpstan-return Listt<TKey, X>
 *
 * @complexity O(N) Lazy.
 *
 * @IgnoreAnnotation("complexity")
 */
function mapMaybe(iterable $list, callable $predicate): Listt
{
	return Listt::fromIter($list)->mapMaybe($predicate);
}

/**
 * Convert `Just null` to `Nothing`.
 *
 * @psalm-template T
 * @phpstan-template T
 *
 * @psalm-param Maybe<T|null> $maybe
 * @phpstan-param Maybe<T|null> $maybe
 *
 * @psalm-return Maybe<T>
 * @phpstan-return Maybe<T>
 *
 * @psalm-pure
 */
function maybeSelectNotNull(Maybe $maybe): Maybe
{
	if ($maybe->isJust() && null === $maybe->fromJust()) {
		return nothing();
	}

	/**
	 * @phpstan-var Maybe<T>
	 */
	return $maybe;
}

/**
 * Convert nullable value to `Maybe`.
 *
 * @psalm-template T
 * @phpstan-template T
 *
 * @psalm-param T|null $value
 * @phpstan-param T|null $value
 *
 * @psalm-return Maybe<T>
 * @phpstan-return Maybe<T>
 *
 * @psalm-pure
 *
 * @param mixed $value
 */
function toMaybe($value): Maybe
{
	return null === $value ? nothing() : just($value);
}
