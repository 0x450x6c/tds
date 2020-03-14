<?php declare(strict_types=1);

namespace TDS\Listt;

use TDS\Maybe\Maybe;

/**
 * Append two lists.
 *
 * If the first list is not finite, the result is the first list.
 *
 * @psalm-pure
 *
 * @template TKey
 * @template TValue
 *
 * @template XKey
 * @template XValue
 *
 * @psalm-param iterable<TKey, TValue> $listA
 * @phpstan-param iterable<TKey, TValue> $listA
 * @phan-param iterable<TKey, TValue> $listA
 *
 * @psalm-param iterable<XKey, XValue> $listB
 * @phpstan-param iterable<XKey, XValue> $listB
 * @phan-param iterable<XKey, XValue> $listB
 *
 * @psalm-return Listt<TKey|XKey, TValue|XValue>
 * @phpstan-return Listt<TKey|XKey, TValue|XValue>
 * @phan-return Listt<TKey|XKey, TValue|XValue>
 *
 * @complexity O(N) Lazy.
 */
function concat(
	iterable $listA,
	iterable $listB,
	bool $preserveNumericKeys = false
): Listt {
	return Listt::fromIter($listA)->concat($listB, $preserveNumericKeys);
}

/**
 * Extract the first element of a list, which must be non-empty.
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 * @template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 * @phan-param iterable<mixed, TValue> $list
 *
 * @throws EmptyListException
 *
 * @psalm-pure
 *
 * @psalm-return TValue
 * @phpstan-return TValue
 * @phan-return TValue
 *
 * @complexity O(1)
 */
function head(iterable $list)
{
	return Listt::fromIter($list)->head();
}

/**
 * Extract the first element of a list.
 *
 * @template TKey
 * @template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 * @phan-param iterable<TKey, TValue> $list
 *
 * @psalm-pure
 *
 * @psalm-return Maybe<TValue>
 * @phpstan-return Maybe<TValue>
 * @phan-return Maybe<TValue>
 *
 * @phan-suppress PhanCommentParamOutOfOrder
 * @phan-suppress PhanTemplateTypeNotUsedInFunctionReturn
 *
 * @complexity O(1)
 */
function headMaybe(iterable $list): Maybe
{
	return Listt::fromIter($list)->headMaybe();
}

/**
 * Extract the last element of a list, which must be finite and non-empty.
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 * @template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 * @phan-param iterable<mixed, TValue> $list
 *
 * @throws EmptyListException
 *
 * @psalm-pure
 *
 * @psalm-return TValue
 * @phpstan-return TValue
 * @phan-return TValue
 *
 * @complexity O(1)
 */
function last(iterable $list)
{
	return Listt::fromIter($list)->last();
}

/**
 * Extract the last element of a list.
 *
 * @template TKey
 * @template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 * @phan-param iterable<TKey, TValue> $list
 *
 * @psalm-pure
 *
 * @psalm-return Maybe<TValue>
 * @phpstan-return Maybe<TValue>
 * @phan-return Maybe<TValue>
 *
 * @phan-suppress PhanCommentParamOutOfOrder
 * @phan-suppress PhanTemplateTypeNotUsedInFunctionReturn
 *
 * @complexity O(1)
 */
function lastMaybe(iterable $list)
{
	return Listt::fromIter($list)->lastMaybe();
}

/**
 * Extract the elements after the head of a list, which must be non-empty.
 *
 * @psalm-pure
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 * @template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 * @phan-param iterable<mixed, TValue> $list
 *
 * @psalm-return Listt<TKey, TValue>
 * @phpstan-return Listt<TKey, TValue>
 * @phan-return Listt<TKey, TValue>
 *
 * @complexity O(N)
 */
function tail(iterable $list, bool $preserveNumericKeys = false): Listt
{
	return Listt::fromIter($list)->tail($preserveNumericKeys);
}

/**
 * Return all the elements of a list except the last one.
 * The list must be non-empty.
 *
 * @template TValue
 * @template TKey
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 * @phan-param iterable<TKey, TValue> $list
 *
 * @psalm-pure
 *
 * @psalm-return Listt<TKey, TValue>
 * @phpstan-return Listt<TKey, TValue>
 * @phan-return Listt<TKey, TValue>
 *
 * @complexity O(N)
 */
function init(iterable $list): Listt
{
	return Listt::fromIter($list)->init();
}

/**
 * Decompose a list into its head and tail.
 *
 * If the list is empty, returns Nothing.
 *
 * If the list is non-empty, returns `Just (x, xs)`,
 *     where `x` is the head of the list and `xs` its tail.
 *
 * @template TKey
 * @template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 * @phan-param iterable<TKey, TValue> $list
 *
 * @psalm-return Maybe<array{TValue, Listt<TKey, TValue>}>
 * @phpstan-return Maybe<array{0: TValue, 1:Listt<TKey, TValue>}>
 * @phan-return Maybe<array{TValue, Listt<TKey, TValue>}>
 */
function uncons(iterable $list): Maybe
{
	return Listt::fromIter($list)->uncons();
}

/**
 * Alias for Listt::count().
 *
 * @psalm-template TKey
 * @psalm-template TValue
 *
 * @phpstan-template TKey
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 * @phan-param iterable<mixed, mixed> $list
 *
 * @psalm-pure
 *
 * @complexity when source is array
 *     or count is specified while creating a list,
 *     then O(1), otherwise O(N).
 */
function length(iterable $list): int
{
	return Listt::fromIter($list)->length();
}

/**
 * Creates a list from function that returns a generator.
 *
 * @template TKey
 * @template TValue
 *
 * @psalm-param \Closure():\Generator<TKey, TValue> $makeGeneratorFn
 * @psalm-param null|int|\Closure():int $count
 *
 * @phpstan-param \Closure():\Generator<TKey, TValue> $makeGeneratorFn
 * @phpstan-param null|int|\Closure():int $count
 *
 * @phan-param \Closure():(\Generator<TKey, TValue>) $makeGeneratorFn
 * @phan-param null|int|\Closure():int $count
 *
 * @param null|\Closure|int $count
 *
 * @phan-suppress PhanCommentParamOutOfOrder
 *
 * @psalm-pure
 *
 * @psalm-return Listt<TKey, TValue>
 * @phpstan-return Listt<TKey, TValue>
 * @phan-return Listt<TKey, TValue>
 *
 * @complexity O(1) just creates a list, but not iterates by.
 */
function fromGenerator(\Closure $makeGeneratorFn, $count = null): Listt
{
	return Listt::fromGenerator($makeGeneratorFn, $count);
}

/**
 * Creates a list from single element.
 *
 * @template TValue
 *
 * @psalm-param TValue $value
 * @phpstan-param TValue $value
 * @phan-param TValue $value
 *
 * @psalm-pure
 *
 * @psalm-return Listt<int, TValue>
 * @phpstan-return Listt<int, TValue>
 * @phan-return Listt<int, TValue>
 *
 * @param mixed $value
 *
 * @complexity O(1)
 */
function from($value): Listt
{
	return Listt::from($value);
}

/**
 * Creates a list from any iterable except generators.
 *
 * @template TKey
 * @template TValue
 *
 * @param null|\Closure|int $count
 *
 * @psalm-param iterable<TKey, TValue> $value
 * @psalm-param null|int|\Closure():int $count
 *
 * @phpstan-param iterable<TKey, TValue> $value
 * @phpstan-param null|int|\Closure():int $count
 *
 * @phan-param iterable<TKey, TValue> $value
 * @phan-param null|int|\Closure():int $count
 *
 * @phan-suppress PhanCommentParamOutOfOrder
 *
 * @psalm-pure
 *
 * @psalm-return Listt<TKey, TValue>
 * @phpstan-return Listt<TKey, TValue>
 * @phan-return Listt<TKey, TValue>
 *
 * @complexity O(N) just creates a list, but not iterates by.
 */
function fromIter(iterable $value, $count = null): Listt
{
	return Listt::fromIter($value, $count);
}

/**
 * Creates an empty list.
 *
 * @psalm-template TKey
 * @psalm-template TValue
 *
 * @psalm-pure
 *
 * @phan-suppress PhanTemplateTypeNotDeclaredInFunctionParams
 *
 * @psalm-return Listt<TKey, TValue>
 * @phpstan-return Listt<TKey, TValue>
 * @phan-return Listt<TKey, TValue>
 *
 * @complexity O(1)
 */
function emptyList(): Listt
{
	/**
	 * @psalm-var iterable<TKey, TValue>
	 */
	$emptyList = [];

	return Listt::fromIter($emptyList, 0);
}

/**
 * Get the Nth element out of a list.
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 * @template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 * @phan-param iterable<mixed, TValue> $list
 *
 * @throws IndexTooLargeException
 *
 * @psalm-pure
 *
 * @psalm-return TValue
 * @phpstan-return TValue
 * @phan-return TValue
 *
 * @complexity O(N) where N = $n.
 */
function nth(iterable $list, int $n)
{
	return Listt::fromIter($list)->nth($n);
}

/**
 * Get a list of all elements that match some condition.
 *
 * This is lazy function,
 *     will be applied only when you are reading data from list.
 *
 * @template TKey
 * @template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 * @phan-param iterable<TKey, TValue> $list
 *
 * @psalm-param \Closure(TValue=, TKey=):bool $predicate
 * @phpstan-param (\Closure(TValue):bool)&(\Closure(TValue, TKey):bool) $predicate
 * @phan-param \Closure(TValue):bool|\Closure(TValue, TKey):bool $predicate
 *
 * @psalm-pure
 *
 * @psalm-return Listt<TKey, TValue>
 * @phpstan-return Listt<TKey, TValue>
 * @phan-return Listt<TKey, TValue>
 *
 * @complexity O(N) Lazy.
 */
function select(iterable $list, \Closure $predicate): Listt
{
	return Listt::fromIter($list)->select($predicate);
}

/**
 * Find the lowest element of a list.
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 * @template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 * @phan-param iterable<mixed, TValue> $list
 *
 * @throws EmptyListException
 *
 * @psalm-pure
 *
 * @phan-suppress PhanTypeMismatchReturnNullable
 * @phan-suppress PhanPartialTypeMismatchReturn
 *
 * @psalm-return TValue
 * @phpstan-return TValue
 * @phan-return TValue
 *
 * @complexity O(N)
 */
function minimum(iterable $list)
{
	return Listt::fromIter($list)->minimum();
}

/**
 * Find the higest element of a list.
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 * @template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 * @phan-param iterable<mixed, TValue> $list
 *
 * @throws EmptyListException
 *
 * @psalm-pure
 *
 * @phan-suppress PhanTypeMismatchReturnNullable
 * @phan-suppress PhanPartialTypeMismatchReturn
 *
 * @psalm-return TValue
 * @phpstan-return TValue
 * @phan-return TValue
 *
 * @complexity O(N)
 */
function maximum(iterable $list)
{
	return Listt::fromIter($list)->maximum();
}

/**
 * Applies passed function to each element of list.
 *
 * @template TKey
 * @template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 * @phan-param iterable<TKey, TValue> $list
 *
 * This is lazy function,
 *     will be applied only when you are reading data from list.
 *
 * @psalm-param \Closure(TValue=, TKey=) $predicate
 * @phpstan-param \Closure(TValue=, TKey=):(void|mixed) $predicate
 * @phan-param \Closure():(void|mixed)|\Closure(TValue):(void|mixed)|\Closure(TValue, TKey):(void|mixed) $predicate
 *
 * @psalm-pure
 *
 * @psalm-return Listt<TKey, TValue>
 * @phpstan-return Listt<TKey, TValue>
 * @phan-return Listt<TKey, TValue>
 *
 * @complexity O(N) Lazy.
 */
function tap(iterable $list, \Closure $predicate): Listt
{
	return Listt::fromIter($list)->tap($predicate);
}

/**
 * Creates a new list populated with the results of calling
 *    a provided function on every element in the calling list.
 *
 * This is lazy function,
 *     will be applied only when you are reading data from list.
 *
 * @template TKey
 * @template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 * @phan-param iterable<TKey, TValue> $list
 *
 * @template X
 *
 * @psalm-param \Closure(TValue=, TKey=):X $predicate
 * @phpstan-param (\Closure(TValue):X)&(\Closure(TValue, TKey):X) $predicate
 * @phan-param (\Closure(TValue):X)|(\Closure(TValue, TKey):X) $predicate
 *
 * @psalm-pure
 *
 * @psalm-return Listt<TKey, X>
 * @phpstan-return Listt<TKey, X>
 * @phan-return Listt<TKey, X>
 *
 * @complexity O(N) Lazy.
 *
 * @phan-suppress PhanTemplateTypeNotUsedInFunctionReturn
 */
function map(iterable $list, \Closure $predicate): Listt
{
	return Listt::fromIter($list)->map($predicate);
}

/**
 * @psalm-pure
 *
 * @psalm-template TKey
 * @psalm-template TValue
 *
 * @phpstan-template TKey
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 * @phan-param iterable<mixed, mixed> $list
 *
 * @complexity O(1)
 */
function null(iterable $list): bool
{
	return Listt::fromIter($list)->null();
}

/**
 * @psalm-pure
 *
 * @psalm-template TKey
 * @psalm-template TValue
 *
 * @phpstan-template TKey
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 * @phan-param iterable<mixed, mixed> $list
 *
 * @complexity O(1)
 */
function isEmpty(iterable $list): bool
{
	return Listt::fromIter($list)->null();
}

/**
 * Turn a list backwards.
 *
 * This is lazy function,
 *     will be applied only when you are reading data from list.
 *
 * @psalm-pure
 *
 * @template TKey
 * @template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 * @phan-param iterable<TKey, TValue> $list
 *
 * @psalm-return Listt<TKey, TValue>
 * @phpstan-return Listt<TKey, TValue>
 * @phan-return Listt<TKey, TValue>
 *
 * @complexity O(2N) Creates one array, and reverse iterate.
 */
function reverse(iterable $list, bool $preserveNumericKeys = false): Listt
{
	return Listt::fromIter($list)->reverse($preserveNumericKeys);
}

/**
 * @psalm-pure
 *
 * @template TKey
 * @template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 * @phan-param iterable<TKey, TValue> $list
 *
 * @psalm-return \Generator<TKey, TValue>
 * @phpstan-return \Generator<TKey, TValue>
 * @phan-return \Generator<TKey, TValue>
 *
 * @complexity O(1)
 */
function toGenerator(iterable $list): \Generator
{
	return Listt::fromIter($list)->toGenerator();
}

/**
 * @psalm-template TKey
 * @phpstan-template TKey
 * @template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 * @phan-param iterable<mixed, TValue> $list
 *
 * @psalm-pure
 *
 * @psalm-return array<array-key, TValue>
 * @phpstan-return array<array-key, TValue>
 * @phan-return array<string|int, TValue>
 *
 * @complexity O(N)
 */
function toArray(iterable $list): array
{
	return Listt::fromIter($list)->toArray();
}

/**
 * Iterates over list applying predicate (if specified).
 *
 * @psalm-template TKey
 * @psalm-template TValue
 *
 * @phpstan-template TKey
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 * @phan-param iterable<mixed, mixed> $list
 *
 * @psalm-param null|\Closure(TValue=, TKey=) $predicate
 * @phpstan-param null|\Closure(TValue=, TKey=):(void|mixed) $predicate
 * @phan-param null|\Closure(mixed=, mixed=):(void|mixed) $predicate
 *
 * @psalm-pure
 *
 * @complexity O(N)
 */
function apply(iterable $list, ?\Closure $predicate = null): void
{
	Listt::fromIter($list)->apply($predicate);
}
