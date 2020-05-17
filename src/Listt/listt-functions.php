<?php declare(strict_types=1);

namespace TDS\Listt;

use function TDS\Maybe\just;
use TDS\Maybe\Maybe;
use function TDS\Maybe\nothing;

/**
 * Append two lists.
 *
 * If the first list is not finite, the result is the first list.
 *
 * @psalm-pure
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-template XKey
 * @phpstan-template XKey
 *
 * @psalm-template XValue
 * @phpstan-template XValue
 *
 * @psalm-param iterable<TKey, TValue> $listA
 * @phpstan-param iterable<TKey, TValue> $listA
 *
 * @psalm-param iterable<XKey, XValue> $listB
 * @phpstan-param iterable<XKey, XValue> $listB
 *
 * @psalm-return Listt<TKey|XKey, TValue|XValue>
 * @phpstan-return Listt<TKey|XKey, TValue|XValue>
 *
 * @complexity O(N) Lazy.
 * @IgnoreAnnotation("complexity")
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
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @throws EmptyListException
 *
 * @psalm-pure
 *
 * @psalm-return TValue
 * @phpstan-return TValue
 *
 * @complexity O(1)
 * @IgnoreAnnotation("complexity")
 */
function head(iterable $list)
{
	return Listt::fromIter($list)->head();
}

/**
 * Extract the first element of a list.
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
 * @psalm-param X $defaultValue
 * @phpstan-param X $defaultValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-pure
 *
 * @psalm-return TValue|X
 * @phpstan-return TValue|X
 *
 * @complexity O(1)
 * @IgnoreAnnotation("complexity")
 *
 * @param mixed $defaultValue
 */
function headOr(iterable $list, $defaultValue)
{
	return Listt::fromIter($list)->headOr($defaultValue);
}

/**
 * Extract the last element of a list, which must be finite and non-empty.
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @throws EmptyListException
 *
 * @psalm-pure
 *
 * @psalm-return TValue
 * @phpstan-return TValue
 *
 * @complexity O(1)
 * @IgnoreAnnotation("complexity")
 */
function last(iterable $list)
{
	return Listt::fromIter($list)->last();
}

/**
 * Extract the last element of a list.
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-pure
 *
 * @psalm-return Maybe<TValue>
 * @phpstan-return Maybe<TValue>
 *
 * @complexity O(1)
 * @IgnoreAnnotation("complexity")
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
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-return Listt<TKey, TValue>
 * @phpstan-return Listt<TKey, TValue>
 *
 * @complexity O(N)
 * @IgnoreAnnotation("complexity")
 */
function tail(iterable $list, bool $preserveNumericKeys = false): Listt
{
	return Listt::fromIter($list)->tail($preserveNumericKeys);
}

/**
 * Return all the elements of a list except the last one.
 * The list must be non-empty.
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-pure
 *
 * @psalm-return Listt<TKey, TValue>
 * @phpstan-return Listt<TKey, TValue>
 *
 * @complexity O(N)
 * @IgnoreAnnotation("complexity")
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
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-return Maybe<array{TValue, Listt<TKey, TValue>}>
 * @phpstan-return Maybe<array{0: TValue, 1:Listt<TKey, TValue>}>
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
 *
 * @psalm-pure
 *
 * @complexity when source is array
 *     or count is specified while creating a list,
 *     then O(1), otherwise O(N).
 * @IgnoreAnnotation("complexity")
 */
function length(iterable $list): int
{
	return Listt::fromIter($list)->length();
}

/**
 * Creates a list from function that returns a generator.
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param callable():\Generator<TKey, TValue> $makeGeneratorFn
 * @psalm-param null|int|callable():int $count
 *
 * @phpstan-param callable():\Generator<TKey, TValue> $makeGeneratorFn
 * @phpstan-param null|int|callable():int $count
 *
 * @param null|callable|int $count
 *
 * @psalm-pure
 *
 * @psalm-return Listt<TKey, TValue>
 * @phpstan-return Listt<TKey, TValue>
 *
 * @complexity O(1) just creates a list, but not iterates by.
 * @IgnoreAnnotation("complexity")
 */
function fromGenerator(callable $makeGeneratorFn, $count = null): Listt
{
	return Listt::fromGenerator($makeGeneratorFn, $count);
}

/**
 * Creates a list from single element.
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param TValue $value
 * @phpstan-param TValue $value
 *
 * @psalm-pure
 *
 * @psalm-return Listt<int, TValue>
 * @phpstan-return Listt<int, TValue>
 *
 * @param mixed $value
 *
 * @complexity O(1)
 * @IgnoreAnnotation("complexity")
 */
function from($value): Listt
{
	return Listt::from($value);
}

/**
 * Creates a list from any iterable except generators.
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @param null|callable|int $count
 *
 * @psalm-param iterable<TKey, TValue> $value
 * @psalm-param null|int|callable():int $count
 *
 * @phpstan-param iterable<TKey, TValue> $value
 * @phpstan-param null|int|callable():int $count
 *
 * @psalm-pure
 *
 * @psalm-return Listt<TKey, TValue>
 * @phpstan-return Listt<TKey, TValue>
 *
 * @complexity O(N) just creates a list, but not iterates by.
 * @IgnoreAnnotation("complexity")
 */
function fromIter(iterable $value, $count = null): Listt
{
	return Listt::fromIter($value, $count);
}

/**
 * Creates a list from any range of numbers.
 *
 * @param ?int $end if null, then list become infinity
 *
 * @psalm-pure
 *
 * @psalm-return Listt<int, int>
 * @phpstan-return Listt<int, int>
 *
 * @complexity O(N).
 */
function fromRange(
	int $start,
	?int $end = null,
	int $step = 1
): Listt {
	return Listt::fromRange($start, $end, $step);
}

/**
 * Creates an empty list.
 *
 * @psalm-template TKey
 * @psalm-template TValue
 *
 * @psalm-pure
 *
 * @psalm-return Listt<TKey, TValue>
 * @phpstan-return Listt<TKey, TValue>
 *
 * @complexity O(1)
 * @IgnoreAnnotation("complexity")
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
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @throws IndexTooLargeException
 *
 * @psalm-pure
 *
 * @psalm-return TValue
 * @phpstan-return TValue
 *
 * @complexity O(N) where N = $n.
 * @IgnoreAnnotation("complexity")
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
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-param callable(TValue=, TKey=):bool $predicate
 * @phpstan-param callable(TValue, TKey|mixed):bool $predicate
 *
 * @psalm-pure
 *
 * @psalm-return Listt<TKey, TValue>
 * @phpstan-return Listt<TKey, TValue>
 *
 * @complexity O(N) Lazy.
 * @IgnoreAnnotation("complexity")
 */
function select(iterable $list, callable $predicate): Listt
{
	return Listt::fromIter($list)->select($predicate);
}

/**
 * Find the lowest element of a list.
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @throws EmptyListException
 *
 * @psalm-pure
 *
 * @psalm-return TValue
 * @phpstan-return TValue
 *
 * @complexity O(N)
 * @IgnoreAnnotation("complexity")
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
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @throws EmptyListException
 *
 * @psalm-pure
 *
 * @psalm-return TValue
 * @phpstan-return TValue
 *
 * @complexity O(N)
 * @IgnoreAnnotation("complexity")
 */
function maximum(iterable $list)
{
	return Listt::fromIter($list)->maximum();
}

/**
 * Applies passed function to each element of list.
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * This is lazy function,
 *     will be applied only when you are reading data from list.
 *
 * @psalm-param callable(TValue=, TKey=) $predicate
 * @phpstan-param callable(TValue=, TKey=):(void|mixed) $predicate
 *
 * @psalm-pure
 *
 * @psalm-return Listt<TKey, TValue>
 * @phpstan-return Listt<TKey, TValue>
 *
 * @complexity O(N) Lazy.
 * @IgnoreAnnotation("complexity")
 */
function tap(iterable $list, callable $predicate): Listt
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
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-template X
 * @phpstan-template X
 *
 * @psalm-param callable(TValue=, TKey=):X $predicate
 * @phpstan-param callable(TValue, TKey|mixed):X $predicate
 *
 * @psalm-pure
 *
 * @psalm-return Listt<TKey, X>
 * @phpstan-return Listt<TKey, X>
 *
 * @complexity O(N) Lazy.
 * @IgnoreAnnotation("complexity")
 */
function map(iterable $list, callable $predicate): Listt
{
	return Listt::fromIter($list)->map($predicate);
}

/**
 * Creates a new list populated with the results of calling
 *    a provided function on every element in the calling list.
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
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-template XKey
 * @phpstan-template XKey
 *
 * @psalm-template XValue
 * @phpstan-template XValue
 *
 * @psalm-param callable(TValue, TKey=):\Generator<XKey, XValue> $predicate
 * @phpstan-param callable(TValue, TKey|mixed):\Generator<XKey, XValue> $predicate
 *
 * @psalm-pure
 *
 * @psalm-return Listt<XKey, XValue>
 * @phpstan-return Listt<XKey, XValue>
 *
 * @complexity O(N) Lazy.
 */
function mapYield(
	iterable $list,
	callable $predicate
): Listt {
	return Listt::fromIter($list)->mapYield($predicate);
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
 *
 * @complexity O(1)
 * @IgnoreAnnotation("complexity")
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
 *
 * @complexity O(1)
 * @IgnoreAnnotation("complexity")
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
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-return Listt<TKey, TValue>
 * @phpstan-return Listt<TKey, TValue>
 *
 * @complexity O(2N) Creates one array, and reverse iterate.
 * @IgnoreAnnotation("complexity")
 */
function reverse(iterable $list, bool $preserveNumericKeys = false): Listt
{
	return Listt::fromIter($list)->reverse($preserveNumericKeys);
}

/**
 * The intersperse function takes an element
 *   and a list and `intersperses' that element
 *   between the elements of the list.
 *
 * This is lazy function,
 *     will be applied only when you are reading data from list.
 *
 * @psalm-pure
 *
 * @psalm-template XValue
 * @phpstan-template XValue
 *
 * @psalm-template XKey
 * @phpstan-template XKey
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-param XValue $value
 * @phpstan-param XValue $value
 *
 * @psalm-param XKey|null $key
 * @phpstan-param XKey|null $key
 *
 * @psalm-return Listt<TKey|XKey, TValue|XValue>
 * @phpstan-return Listt<TKey|XKey, TValue|XValue>
 *
 * @complexity O(N).
 *
 * @param mixed      $value
 * @param null|mixed $key
 */
function intersperse(
	iterable $list,
	$value,
	$key = null,
	bool $preserveNumericKeys = false
): Listt {
	/**
	 * @psalm-var Listt<TKey|XKey, TValue|XValue>
	 * @phpstan-var Listt<TKey|XKey, TValue|XValue>
	 */
	return Listt::fromIter($list)->intersperse($value, $key, $preserveNumericKeys);
}

/**
 * Left-associative fold of a structure.
 *
 * @psalm-template A
 * @phpstan-template A
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-param callable(A, TValue, TKey=):A $predicate
 * @phpstan-param callable(A, TValue):A $predicate
 *
 * @psalm-param A $initialValue
 * @phpstan-param A $initialValue
 *
 * @psalm-pure
 *
 * @psalm-return A
 * @phpstan-return A
 *
 * @complexity O(N).
 *
 * @param mixed $initialValue
 */
function foldl(iterable $list, callable $predicate, $initialValue)
{
	return Listt::fromIter($list)->foldl($predicate, $initialValue);
}

/**
 * A variant of foldl that has no base case,
 *   and thus may only be applied to non-empty structures.
 *
 * @psalm-template A of TValue
 * @phpstan-template A
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-param callable(A|TValue, TValue, TKey=):A $predicate
 * @phpstan-param callable(A|TValue, TValue):A $predicate
 *
 * @psalm-pure
 *
 * @psalm-return A|TValue
 * @phpstan-return A|TValue
 *
 * @complexity O(N).
 */
function foldl1(iterable $list, callable $predicate)
{
	return Listt::fromIter($list)->foldl1($predicate);
}

/**
 * Right-associative fold of a structure.
 *
 * @psalm-template A
 * @phpstan-template A
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-param callable(A, TValue, TKey=):A $predicate
 *
 * @psalm-param A $initialValue
 * @phpstan-param A $initialValue
 *
 * @psalm-pure
 *
 * @psalm-return A
 * @phpstan-return A
 *
 * @complexity O(N).
 *
 * @param mixed $initialValue
 */
function foldr(
	iterable $list,
	callable $predicate,
	$initialValue,
	bool $preserveNumericKeys = false
) {
	return Listt::fromIter($list)->foldr(
		$predicate,
		$initialValue,
		$preserveNumericKeys
	);
}

/**
 * A variant of foldr that has no base case,
 *   and thus may only be applied to non-empty structures.
 *
 * @psalm-template A of TValue
 * @phpstan-template A
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-param callable(A|TValue, TValue, TKey=):A $predicate
 *
 * @psalm-pure
 *
 * @psalm-return A|TValue
 * @phpstan-return A|TValue
 *
 * @complexity O(N).
 */
function foldr1(iterable $list, callable $predicate)
{
	return Listt::fromIter($list)->foldr1($predicate);
}

/**
 * The sum function computes the sum of the numbers of a structure.
 *
 * @psalm-pure
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue of int|float
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-return TValue
 * @phpstan-return TValue
 *
 * @complexity O(N).
 */
function sum(iterable $list)
{
	/**
	 * @psalm-var TValue
	 */
	return Listt::fromIter($list)->sum();
}

/**
 * The product function computes the product of the numbers of a structure.
 *
 * @psalm-pure
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue of int|float
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-return TValue
 * @phpstan-return TValue
 *
 * @complexity O(N).
 */
function product(iterable $list)
{
	/**
	 * @psalm-var TValue
	 */
	return Listt::fromIter($list)->product();
}

/**
 * Map a function over all the elements of a container and concatenate the resulting lists.
 *
 * This is lazy function,
 *     will be applied only when you are reading data from list.
 *
 * @psalm-template XKey
 * @phpstan-template XKey
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-template XValue
 * @phpstan-template XValue
 *
 * @psalm-param callable(TValue, TKey=):iterable<XKey, XValue> $predicate
 * @phpstan-param callable(TValue, TKey|mixed):iterable<XKey, XValue> $predicate
 *
 * @psalm-pure
 *
 * @psalm-return Listt<XKey, XValue>
 * @phpstan-return Listt<XKey, XValue>
 *
 * @complexity O(N) Lazy.
 */
function concatMap(
	iterable $list,
	callable $predicate,
	bool $preserveNumericKeys = false
): Listt {
	return Listt::fromIter($list)->concatMap($predicate, $preserveNumericKeys);
}

/**
 * Determines whether any element of the structure satisfies the predicate.
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-param callable(TValue, TKey=):bool $predicate
 * @phpstan-param callable(TValue, TKey|mixed):bool $predicate
 *
 * @psalm-pure
 *
 * @complexity O(N).
 */
function any(
	iterable $list,
	callable $predicate
): bool {
	return Listt::fromIter($list)->any($predicate);
}

/**
 * Determines whether any element of the structure satisfies the passed element.
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-param TValue $element
 * @phpstan-param TValue $element
 *
 * @psalm-pure
 *
 * @complexity O(N).
 *
 * @param mixed $element
 */
function contains(
	iterable $list,
	$element
): bool {
	return Listt::fromIter($list)->contains($element);
}

/**
 * Determines whether any key of the structure satisfies the passed key.
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-param TKey $key
 * @phpstan-param TKey $key
 *
 * @psalm-pure
 *
 * @complexity O(N).
 *
 * @param mixed $key
 */
function containsKey(
	iterable $list,
	$key
): bool {
	return Listt::fromIter($list)->containsKey($key);
}

/**
 * Take n, applied to a list xs,
 *     returns the prefix of xs of length n, or xs itself if n > length xs:.
 *
 * @psalm-pure
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-return Listt<TKey, TValue>
 * @phpstan-return Listt<TKey, TValue>
 *
 * @IgnoreAnnotation("complexity")
 *
 * @complexity O(N).
 */
function take(iterable $list, int $n, bool $preserveNumericKeys = false): Listt
{
	return fromIter($list)->take($n, $preserveNumericKeys);
}

/**
 * @psalm-pure
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue> $list
 * @phpstan-param iterable<TKey, TValue> $list
 *
 * @psalm-return \Generator<TKey, TValue>
 * @phpstan-return \Generator<TKey, TValue>
 *
 * @complexity O(1)
 * @IgnoreAnnotation("complexity")
 */
function toGenerator(iterable $list): \Generator
{
	return Listt::fromIter($list)->toGenerator();
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
 * @psalm-pure
 *
 * @psalm-return array<array-key, TValue>
 * @phpstan-return array<array-key, TValue>
 *
 * @complexity O(N)
 * @IgnoreAnnotation("complexity")
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
 *
 * @psalm-param null|callable(TValue=, TKey=) $predicate
 * @phpstan-param null|callable(TValue=, TKey=):(void|mixed) $predicate
 *
 * @psalm-pure
 *
 * @complexity O(N)
 * @IgnoreAnnotation("complexity")
 */
function apply(iterable $list, ?callable $predicate = null): void
{
	Listt::fromIter($list)->apply($predicate);
}

/**
 * Selects not null items of list.
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @psalm-param iterable<TKey, TValue|null> $list
 * @phpstan-param iterable<TKey, TValue|null> $list
 *
 * @psalm-return Listt<TKey, TValue>
 * @phpstan-return Listt<TKey, TValue>
 *
 * @psalm-pure
 */
function listSelectNotNull(iterable $list): Listt
{
	return Listt::fromIter($list)
		->mapMaybe(static fn ($x) => null === $x ? nothing() : just($x))
	;
}
