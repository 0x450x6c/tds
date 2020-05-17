<?php

declare(strict_types=1);

namespace TDS\Listt;

use function TDS\Maybe\catMaybes;
use function TDS\Maybe\just;
use TDS\Maybe\Maybe;
use function TDS\Maybe\nothing;
use TDS\Ord;

/**
 * Clone of https://hackage.haskell.org/package/base-4.12.0.0/docs/Data-List.html.
 *
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 *
 * @template-implements \Iterator<TKey, TValue>
 * @psalm-immutable
 *
 * @IgnoreAnnotation("complexity")
 */
class Listt implements \Iterator, \Countable, \Serializable
{
	/**
	 * @psalm-var callable():\Generator<TKey, TValue>
	 * @phpstan-var callable():\Generator<TKey, TValue>
	 *
	 * @psalm-allow-private-mutation
	 */
	private $makeGeneratorFn;

	/**
	 * @psalm-var null|\Generator<TKey, TValue>
	 * @phpstan-var null|\Generator<TKey, TValue>
	 *
	 * @psalm-allow-private-mutation
	 */
	private ?\Generator $generator = null;

	/**
	 * @psalm-var null|\Generator<TKey, TValue>
	 * @phpstan-var null|\Generator<TKey, TValue>
	 *
	 * @psalm-allow-private-mutation
	 */
	private ?\Generator $generatorForIterator = null;

	/**
	 * @psalm-allow-private-mutation
	 */
	private ?bool $isEmpty = null;

	/**
	 * @psalm-allow-private-mutation
	 */
	private ?int $count = null;

	/**
	 * @psalm-var null|callable():int
	 * @phpstan-var null|callable():int
	 * @psalm-allow-private-mutation
	 */
	private $countFn;

	/**
	 * @psalm-param callable():\Generator<TKey, TValue> $makeGeneratorFn
	 * @phpstan-param callable():\Generator<TKey, TValue> $makeGeneratorFn
	 *
	 * @psalm-param null|int|callable():int $count
	 * @phpstan-param null|int|callable():int $count
	 *
	 * @param null|callable|int $count
	 *
	 * @psalm-pure
	 */
	protected function __construct(
		callable $makeGeneratorFn,
		$count = null
	) {
		/** @psalm-suppress ImpurePropertyAssignment */
		$this->makeGeneratorFn = $makeGeneratorFn;
		if (null !== $count) {
			if (\is_int($count)) {
				$this->count = $count;

				if (null === $this->isEmpty) {
					$this->isEmpty = 0 === $this->count;
				}
			} else {
				/** @psalm-suppress ImpurePropertyAssignment */
				$this->countFn = $count;
			}
		}
	}

	/**
	 * Same as Listt::apply().
	 *
	 * @psalm-param null|callable(TValue=, TKey=) $predicate
	 * @phpstan-param null|callable(TValue=, TKey=):(void|mixed) $predicate
	 *
	 * @psalm-pure
	 *
	 * @complexity O(N)
	 */
	public function __invoke(?callable $predicate = null): void
	{
		$this->apply($predicate);
	}

	/**
	 * Append two lists.
	 *
	 * If the first list is not finite, the result is the first list.
	 *
	 * @psalm-pure
	 *
	 * @psalm-template XKey
	 * @phpstan-template XKey
	 *
	 * @psalm-template XValue
	 * @phpstan-template XValue
	 *
	 * @psalm-param iterable<XKey, XValue> $list
	 * @phpstan-param iterable<XKey, XValue> $list
	 *
	 * @psalm-return Listt<TKey|XKey, TValue|XValue>
	 * @phpstan-return Listt<TKey|XKey, TValue|XValue>
	 *
	 * @complexity O(N) Lazy.
	 */
	public function concat(
		iterable $list,
		bool $preserveNumericKeys = false
	): self {
		if (!$list instanceof self) {
			/**
			 * @psalm-var Listt<XKey, XValue>
			 */
			$list = self::fromIter($list);
		}

		/**
		 * @phpstan-var callable():\Generator<TKey|XKey, TValue|XValue>
		 */
		$makeGeneratorFn = function () use (
			$preserveNumericKeys,
			$list
		): \Generator {
			foreach ($this->toGenerator() as $k => $v) {
				if (!$preserveNumericKeys && \is_int($k)) {
					yield $v;

					continue;
				}

				yield $k => $v;
			}

			foreach ($list as $k => $v) {
				if (!$preserveNumericKeys && \is_int($k)) {
					yield $v;

					continue;
				}

				yield $k => $v;
			}
		};

		return self::fromGenerator(
			$makeGeneratorFn,
			fn () => $this->count() + $list->count()
		);
	}

	/**
	 * Extract the first element of a list, which must be non-empty.
	 *
	 * @throws EmptyListException
	 *
	 * @psalm-pure
	 *
	 * @psalm-return TValue
	 * @phpstan-return TValue
	 *
	 * @complexity O(1)
	 */
	public function head()
	{
		if ($this->null()) {
			throw new EmptyListException(
				'Empty list.'
			);
		}

		/** @psalm-suppress ImpureMethodCall */
		return $this->getGenerator()->current();
	}

	/**
	 * Extract the first element of a list.
	 *
	 * @psalm-pure
	 *
	 * @psalm-template X
	 * @phpstan-template X
	 *
	 * @psalm-param X $defaultValue
	 * @phpstan-param X $defaultValue
	 *
	 * @psalm-return TValue|X
	 * @phpstan-return TValue|X
	 *
	 * @complexity O(1)
	 *
	 * @param mixed $defaultValue
	 */
	public function headOr($defaultValue)
	{
		if ($this->null()) {
			return $defaultValue;
		}

		return $this->head();
	}

	/**
	 * Extract the last element of a list, which must be finite and non-empty.
	 *
	 * @throws EmptyListException
	 *
	 * @psalm-pure
	 *
	 * @psalm-return TValue
	 * @phpstan-return TValue
	 *
	 * @complexity O(N)
	 */
	public function last()
	{
		if ($this->null()) {
			throw new EmptyListException(
				'Empty list.'
			);
		}

		$generator = $this->toGenerator();
		/** @psalm-suppress ImpureMethodCall */
		$last = $generator->current();
		/** @psalm-suppress ImpureMethodCall */
		while ($generator->valid()) {
			$last = $generator->current();
			$generator->next();
		}

		/** @psalm-suppress ImpureMethodCall */
		return $last;
	}

	/**
	 * Extract the last element of a list.
	 *
	 * @psalm-pure
	 *
	 * @psalm-return Maybe<TValue>
	 * @phpstan-return Maybe<TValue>
	 *
	 * @complexity O(N)
	 */
	public function lastMaybe(): Maybe
	{
		if ($this->null()) {
			return nothing();
		}

		return just($this->last());
	}

	/**
	 * Extract the elements after the head of a list, which must be non-empty.
	 *
	 * @psalm-pure
	 *
	 * @psalm-return Listt<TKey, TValue>
	 * @phpstan-return Listt<TKey, TValue>
	 *
	 * @complexity O(N)
	 */
	public function tail(bool $preserveNumericKeys = false): self
	{
		if ($this->null()) {
			throw new EmptyListException();
		}

		/**
		 * @phpstan-var callable():\Generator<TKey, TValue>
		 */
		$makeGeneratorFn = function () use ($preserveNumericKeys): \Generator {
			$generator = $this->toGenerator();
			/** @psalm-suppress ImpureMethodCall */
			$generator->next();
			/** @psalm-suppress ImpureMethodCall */
			while ($generator->valid()) {
				$k = $generator->key();
				$v = $generator->current();
				if (!$preserveNumericKeys && \is_int($k)) {
					yield $v;

					$generator->next();

					continue;
				}

				yield $k => $v;
				$generator->next();
			}
		};

		return self::fromGenerator(
			$makeGeneratorFn,
			fn () => $this->count() - 1
		);
	}

	/**
	 * Return all the elements of a list except the last one.
	 * The list must be non-empty.
	 *
	 * @psalm-pure
	 *
	 * @psalm-return Listt<TKey, TValue>
	 * @phpstan-return Listt<TKey, TValue>
	 *
	 * @complexity O(N)
	 */
	public function init(): self
	{
		if ($this->null()) {
			throw new EmptyListException();
		}

		/**
		 * @phpstan-var callable():\Generator<TKey, TValue>
		 */
		$makeGeneratorFn = function (): \Generator {
			$generator = $this->toGenerator();
			/** @psalm-suppress ImpureMethodCall */
			while (true) {
				$k = $generator->key();
				$v = $generator->current();
				$generator->next();
				if ($generator->valid()) {
					yield $k => $v;
				} else {
					break;
				}
			}
		};

		return self::fromGenerator(
			$makeGeneratorFn,
			fn () => $this->count() - 1
		);
	}

	/**
	 * Decompose a list into its head and tail.
	 *
	 * If the list is empty, returns Nothing.
	 *
	 * If the list is non-empty, returns `Just (x, xs)`,
	 *     where `x` is the head of the list and `xs` its tail.
	 *
	 * @psalm-return Maybe<array{TValue, Listt<TKey, TValue>}>
	 * @phpstan-return Maybe<array{0: TValue, 1:Listt<TKey, TValue>}>
	 */
	public function uncons(): Maybe
	{
		if ($this->null()) {
			return nothing();
		}

		/**
		 * @phpstan-var Maybe<array{0: TValue, 1:Listt<TKey, TValue>}>
		 */
		return just([$this->head(), $this->tail()]);
	}

	/**
	 * @psalm-pure
	 *
	 * @complexity O(1)
	 */
	public function null(): bool
	{
		if (null === $this->isEmpty) {
			/**
			 * @psalm-suppress InaccessibleProperty
			 * @psalm-suppress ImpureMethodCall
			 */
			$this->isEmpty = !$this->getGenerator()->valid();
			if ($this->isEmpty) {
				// Generator will be closed after call the
				//   method `valid' if it is empty.
				// So, we'll re-craete empty generator.
				/** @var iterable<TKey, TValue> */
				$emptyList = [];
				/** @psalm-suppress ImpurePropertyAssignment */
				$this->generator = self::yieldFromIter($emptyList);
			}
		}

		return $this->isEmpty;
	}

	/**
	 * Alias for Listt::null().
	 *
	 * @psalm-pure
	 *
	 * @complexity O(1)
	 */
	public function isEmpty(): bool
	{
		return $this->null();
	}

	/**
	 * Alias for Listt::count().
	 *
	 * @psalm-pure
	 *
	 * @complexity when source is array
	 *     or count is specified while creating a list,
	 *     then O(1), otherwise O(N).
	 */
	public function length(): int
	{
		return $this->count();
	}

	/**
	 * @psalm-return Maybe<TValue>
	 * @phpstan-return Maybe<TValue>
	 *
	 * @psalm-pure
	 */
	public function toMaybe(): Maybe
	{
		if ($this->null()) {
			return nothing();
		}

		return just($this->head());
	}

	/**
	 * Creates an empty list.
	 *
	 * @psalm-template XKey
	 * @phpstan-template XKey
	 *
	 * @psalm-template XValue
	 * @phpstan-template XValue
	 *
	 * @psalm-pure
	 *
	 * @psalm-return Listt<XKey, XValue>
	 * @phpstan-return Listt<XKey, XValue>
	 *
	 * @complexity O(1)
	 */
	public static function emptyList(): self
	{
		/**
		 * @phpstan-var iterable<XKey, XValue>
		 * @psalm-var iterable<XKey, XValue>
		 */
		$emptyList = [];

		return self::fromIter($emptyList, 0);
	}

	/**
	 * Creates a list from function that returns a generator.
	 *
	 * @psalm-template XKey
	 * @phpstan-template XKey
	 *
	 * @psalm-template XValue
	 * @phpstan-template XValue
	 *
	 * @psalm-param callable():\Generator<XKey, XValue> $makeGeneratorFn
	 * @phpstan-param callable():\Generator<XKey, XValue> $makeGeneratorFn
	 *
	 * @psalm-param null|int|callable():int $count
	 * @phpstan-param null|int|callable():int $count
	 *
	 * @param null|callable|int $count
	 *
	 * @psalm-pure
	 *
	 * @psalm-return Listt<XKey, XValue>
	 * @phpstan-return Listt<XKey, XValue>
	 *
	 * @complexity O(1) just creates a list, but not iterates by.
	 */
	public static function fromGenerator(
		callable $makeGeneratorFn,
		$count = null
	): self {
		return new self($makeGeneratorFn, $count);
	}

	/**
	 * Creates a list from single element.
	 *
	 * @psalm-template XValue
	 * @phpstan-template XValue
	 *
	 * @psalm-param XValue $value
	 * @phpstan-param XValue $value
	 *
	 * @psalm-pure
	 *
	 * @psalm-return Listt<int, XValue>
	 * @phpstan-return Listt<int, XValue>
	 *
	 * @param mixed $value
	 *
	 * @complexity O(1)
	 */
	public static function from($value): self
	{
		return self::fromIter([$value], 1);
	}

	/**
	 * Creates a list from any iterable except generators.
	 *
	 * @psalm-template XKey
	 * @phpstan-template XKey
	 *
	 * @psalm-template XValue
	 * @phpstan-template XValue
	 *
	 * @param null|callable|int $count
	 *
	 * @psalm-param iterable<XKey, XValue> $value
	 * @psalm-param null|int|callable():int $count
	 *
	 * @phpstan-param iterable<XKey, XValue> $value
	 * @phpstan-param null|int|callable():int $count
	 *
	 * @psalm-pure
	 *
	 * @psalm-return Listt<XKey, XValue>
	 * @phpstan-return Listt<XKey, XValue>
	 *
	 * @complexity O(1) just creates a list, but not iterates by.
	 */
	public static function fromIter(
		iterable $value,
		$count = null
	): self {
		if ($value instanceof self) {
			return $value;
		}

		if (null === $count) {
			if (\is_array($value)) {
				/** @psalm-suppress MixedArgumentTypeCoercion */
				$count = \count($value);
			} elseif ($value instanceof \Countable) {
				$count = static fn (): int => \count($value);
			}
		}

		return self::fromGenerator(
			static fn () => self::yieldFromIter($value),
			$count
		);
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
	public static function fromRange(
		int $start,
		?int $end = null,
		int $step = 1
	): self {
		/**
		 * @psalm-var callable():\Generator<int, int>
		 */
		$makeGeneratorFn = static function () use (
			$start,
			$end,
			$step
		): \Generator {
			$current = $start;
			while (null === $end || $end > $current) {
				yield $current;
				$current += $step;
			}
		};

		return self::fromGenerator(
			$makeGeneratorFn
		);
	}

	/**
	 * Iterates over list applying predicate (if specified).
	 *
	 * @psalm-param null|callable(TValue=, TKey=) $predicate
	 * @phpstan-param null|callable(TValue=, TKey=):(void|mixed) $predicate
	 *
	 * @psalm-pure
	 *
	 * @complexity O(N)
	 */
	public function apply(?callable $predicate = null): void
	{
		if (null !== $predicate) {
			if (null === $this->count) {
				$count = 0;
				foreach ($this->toGenerator() as $k => $v) {
					$predicate($v, $k);
					++$count;
				}

				/** @psalm-suppress InaccessibleProperty */
				$this->count = $count;
			} else {
				foreach ($this->toGenerator() as $k => $v) {
					$predicate($v, $k);
				}
			}

			return;
		}

		if (null === $this->count) {
			/** @psalm-suppress InaccessibleProperty */
			$this->count = iterator_count($this->toGenerator());
		} else {
			$generator = $this->toGenerator();
			/** @psalm-suppress ImpureMethodCall */
			while ($generator->valid()) {
				$generator->next();
			}
		}
	}

	/**
	 * Get the Nth element out of a list.
	 *
	 * @throws EmptyListException
	 *
	 * @psalm-pure
	 *
	 * @psalm-return TValue
	 * @phpstan-return TValue
	 *
	 * @complexity O(N) where N = $n.
	 */
	public function nth(int $n)
	{
		if ($n < 0) {
			throw new \InvalidArgumentException(sprintf(
				'Argument $n must be natural number, %d given.',
				$n
			));
		}

		if ($this->null()) {
			throw new IndexTooLargeException($n, 0);
		}

		if (0 === $n) {
			return $this->head();
		}

		$generator = $this->toGenerator();
		/** @psalm-suppress ImpureMethodCall */
		for ($i = 0; $i < $n; ++$i) {
			$generator->next();

			if (!$generator->valid()) {
				throw new IndexTooLargeException($n, $i - 1);
			}
		}

		/** @psalm-suppress ImpureMethodCall */
		return $generator->current();
	}

	/**
	 * Get a list of all elements that match some condition.
	 *
	 * This is lazy function,
	 *     will be applied only when you are reading data from list.
	 *
	 * @psalm-param callable(TValue, TKey=):bool $predicate
	 * @phpstan-param callable(TValue, TKey|mixed):bool $predicate
	 *
	 * @psalm-pure
	 *
	 * @psalm-return Listt<TKey, TValue>
	 * @phpstan-return Listt<TKey, TValue>
	 *
	 * @complexity O(N) Lazy.
	 */
	public function select(
		callable $predicate,
		bool $preserveNumericKeys = false
	): self {
		/**
		 * @phpstan-var callable():\Generator<TKey, TValue>
		 */
		$generator = function () use (
			$predicate,
			$preserveNumericKeys
		): \Generator {
			foreach ($this->toGenerator() as $k => $v) {
				if (!\call_user_func_array($predicate, [$v, $k])) {
					continue;
				}

				if (!$preserveNumericKeys && \is_int($k)) {
					yield $v;

					continue;
				}

				yield $k => $v;
			}
		};

		return self::fromGenerator(
			$generator
		);
	}

	/**
	 * Find the lowest element of a list.
	 *
	 * @throws EmptyListException
	 *
	 * @psalm-pure
	 *
	 * @psalm-return TValue
	 * @phpstan-return TValue
	 *
	 * @complexity O(N)
	 */
	public function minimum()
	{
		if ($this->null()) {
			throw new EmptyListException(
				'Empty list.'
			);
		}

		$generator = $this->toGenerator();
		/** @psalm-suppress ImpureMethodCall */
		$minimum = $generator->current();
		/** @psalm-suppress ImpureMethodCall */
		foreach ($generator as $v) {
			if (Ord::LT === self::compare($v, $minimum)) {
				$minimum = $v;
			}
		}

		/**
		 * @psalm-var TValue
		 */
		return $minimum;
	}

	/**
	 * Find the higest element of a list.
	 *
	 * @throws EmptyListException
	 *
	 * @psalm-pure
	 *
	 * @psalm-return TValue
	 * @phpstan-return TValue
	 *
	 * @complexity O(N)
	 */
	public function maximum()
	{
		if ($this->null()) {
			throw new EmptyListException(
				'Empty list.'
			);
		}

		$generator = $this->toGenerator();
		/** @psalm-suppress ImpureMethodCall */
		$maximum = $generator->current();
		/** @psalm-suppress ImpureMethodCall */
		foreach ($generator as $v) {
			if (Ord::GT === self::compare($v, $maximum)) {
				$maximum = $v;
			}
		}

		/**
		 * @psalm-var TValue
		 */
		return $maximum;
	}

	/**
	 * Applies passed function to each element of list.
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
	 */
	public function tap(callable $predicate): self
	{
		/**
		 * @phpstan-var callable():\Generator<TKey, TValue>
		 */
		$generator = function () use ($predicate): \Generator {
			foreach ($this->toGenerator() as $k => $v) {
				$predicate($v, $k);

				yield $k => $v;
			}
		};

		return self::fromGenerator(
			$generator,
			$this->count ?? $this->countFn
		);
	}

	/**
	 * Creates a new list populated with the results of calling
	 *    a provided function on every element in the calling list.
	 *
	 * This is lazy function,
	 *     will be applied only when you are reading data from list.
	 *
	 * @psalm-template X
	 * @phpstan-template X
	 *
	 * @psalm-param callable(TValue, TKey=):X $predicate
	 * @phpstan-param callable(TValue, TKey|mixed):X $predicate
	 *
	 * @psalm-pure
	 *
	 * @psalm-return Listt<TKey, X>
	 * @phpstan-return Listt<TKey, X>
	 *
	 * @complexity O(N) Lazy.
	 */
	public function map(callable $predicate): self
	{
		/**
		 * @phpstan-var callable():\Generator<TKey, X>
		 */
		$makeGeneratorFn = function () use ($predicate): \Generator {
			foreach ($this->toGenerator() as $k => $v) {
				yield $k => \call_user_func_array($predicate, [$v, $k]);
			}
		};

		return self::fromGenerator(
			$makeGeneratorFn,
			$this->count ?? $this->countFn
		);
	}

	/**
	 * Creates a new list populated with the results of calling
	 *    a provided function on every element in the calling list.
	 *
	 * This is lazy function,
	 *     will be applied only when you are reading data from list.
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
	public function mapYield(callable $predicate): self
	{
		/**
		 * @phpstan-var callable():\Generator<XKey, XValue>
		 */
		$makeGeneratorFn = function () use ($predicate): \Generator {
			foreach ($this->toGenerator() as $k => $v) {
				yield from \call_user_func_array($predicate, [$v, $k]);
			}
		};

		return self::fromGenerator(
			$makeGeneratorFn
		);
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
	 * @psalm-template X
	 * @phpstan-template X
	 *
	 * @psalm-param callable(TValue, TKey=):Maybe<X> $predicate
	 * @phpstan-param callable(TValue, TKey|mixed):Maybe<X> $predicate
	 *
	 * @psalm-pure
	 *
	 * @psalm-return Listt<TKey, X>
	 * @phpstan-return Listt<TKey, X>
	 *
	 * @complexity O(N) Lazy.
	 */
	public function mapMaybe(callable $predicate): self
	{
		return catMaybes(
			$this->map($predicate)
		);
	}

	/**
	 * Turn a list backwards.
	 *
	 * This is lazy function,
	 *     will be applied only when you are reading data from list.
	 *
	 * @psalm-pure
	 *
	 * @psalm-return Listt<TKey, TValue>
	 * @phpstan-return Listt<TKey, TValue>
	 *
	 * @complexity O(2N).
	 */
	public function reverse(bool $preserveNumericKeys = false): self
	{
		/** @psalm-var callable():\Generator<TKey, TValue> */
		$makeGeneratorFn = function () use ($preserveNumericKeys): \Generator {
			$list = $this->toArray();
			if (0 === \count($list)) {
				return;
			}

			for (end($list); null !== ($key = key($list)); prev($list)) {
				if (!$preserveNumericKeys && \is_int($key)) {
					yield $list[$key];

					continue;
				}

				yield $key => $list[$key];
			}
		};

		if (null !== $this->count) {
			$count = $this->count;
		} elseif (null !== $this->countFn) {
			$count = $this->countFn;
		} else {
			$count = fn (): int => $this->count();
		}

		return self::fromGenerator(
			$makeGeneratorFn,
			$count
		);
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
	public function intersperse(
		$value,
		$key = null,
		bool $preserveNumericKeys = false
	): self {
		/** @psalm-var callable():\Generator<TKey|XKey, TValue|XValue> */
		$makeGeneratorFn = function () use (
			$value,
			$key,
			$preserveNumericKeys
		): \Generator {
			if ($this->isEmpty()) {
				return;
			}

			$generator = $this->toGenerator();
			while (true) {
				/** @psalm-suppress ImpureMethodCall */
				$k = $generator->key();
				/** @psalm-suppress ImpureMethodCall */
				$v = $generator->current();

				if (!$preserveNumericKeys && \is_int($k)) {
					yield $v;
				} else {
					yield $k => $v;
				}

				/** @psalm-suppress ImpureMethodCall */
				$generator->next();
				/** @psalm-suppress ImpureMethodCall */
				if (!$generator->valid()) {
					break;
				}

				if (null === $key) {
					yield $value;
				} else {
					yield $key => $value;
				}
			}
		};

		$count = fn (): int => ($this->count() * 2) - 1;

		return self::fromGenerator(
			$makeGeneratorFn,
			$count
		);
	}

	/**
	 * Left-associative fold of a structure.
	 *
	 * @psalm-template A
	 * @phpstan-template A
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
	public function foldl(callable $predicate, $initialValue)
	{
		foreach ($this->toGenerator() as $k => $v) {
			$initialValue = \call_user_func_array(
				$predicate,
				[$initialValue, $v, $k]
			);
		}

		return $initialValue;
	}

	/**
	 * A variant of foldl that has no base case,
	 *   and thus may only be applied to non-empty structures.
	 *
	 * @psalm-template A of TValue
	 * @phpstan-template A
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
	public function foldl1(callable $predicate)
	{
		if ($this->null()) {
			throw new EmptyListException(
				'Empty list.'
			);
		}

		$head = $this->head();
		$tail = $this->tail();

		return $tail->foldl($predicate, $head);
	}

	/**
	 * Right-associative fold of a structure.
	 *
	 * @psalm-template A
	 * @phpstan-template A
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
	public function foldr(
		callable $predicate,
		$initialValue,
		bool $preserveNumericKeys = false
	) {
		foreach ($this->reverse($preserveNumericKeys) as $k => $v) {
			$initialValue = \call_user_func_array(
				$predicate,
				[$initialValue, $v, $k]
			);
		}

		return $initialValue;
	}

	/**
	 * The sum function computes the sum of the numbers of a structure.
	 *
	 * @psalm-pure
	 *
	 * @psalm-return (
	 *     TValue is int ? int : (
	 *         TValue is float ? float : (
	 *             TValue is numeric ? float : null
	 *         )
	 *     )
	 * )
	 * @phpstan-return TValue
	 *
	 * @complexity O(N).
	 */
	public function sum()
	{
		if ($this->isEmpty()) {
			/**
			 * @psalm-var int
			 * @phpstan-var TValue
			 */
			return 0;
		}

		if (self::assertNumericList($this)) {
			/**
			 * @phpstan-var Listt<TKey, int|float> $this
			 */
			return $this->foldl1([self::class, '_add']);
		}

		throw new \RuntimeException(
			'Only numeric lists can be summarized.'
		);
	}

	/**
	 * The product function computes the product of the numbers of a structure.
	 *
	 * @psalm-pure
	 *
	 * @psalm-return (
	 *     TValue is int ? int : (
	 *         TValue is float ? float : (
	 *             TValue is numeric ? float : null
	 *         )
	 *     )
	 * )
	 * @phpstan-return TValue
	 *
	 * @complexity O(N).
	 */
	public function product()
	{
		if ($this->isEmpty()) {
			/**
			 * @psalm-var int
			 * @phpstan-var TValue
			 */
			return 0;
		}

		if (self::assertNumericList($this)) {
			/**
			 * @phpstan-var Listt<TKey, int|float> $this
			 */
			return $this->foldl1([self::class, '_multiply']);
		}

		throw new \RuntimeException(
			'The product function can be applied only for list of numbers.'
		);
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
	public function concatMap(
		callable $predicate,
		bool $preserveNumericKeys = false
	): self {
		/**
		 * @phpstan-var callable():\Generator<XKey, XValue>
		 */
		$makeGeneratorFn = function () use (
			$predicate,
			$preserveNumericKeys
		): \Generator {
			foreach ($this->toGenerator() as $k => $v) {
				$childList = \call_user_func_array($predicate, [$v, $k]);
				foreach ($childList as $childListKey => $childListValue) {
					if (!$preserveNumericKeys && \is_int($childListKey)) {
						yield $childListValue;

						continue;
					}

					yield $childListKey => $childListValue;
				}
			}
		};

		return self::fromGenerator(
			$makeGeneratorFn,
			$this->count ?? $this->countFn
		);
	}

	/**
	 * A variant of foldr that has no base case,
	 *   and thus may only be applied to non-empty structures.
	 *
	 * This is lazy function,
	 *     will be applied only when you are reading data from list.
	 *
	 * @psalm-template A of TValue
	 * @phpstan-template A
	 *
	 * @psalm-param callable(A|TValue, TValue, TKey=):A $predicate
	 *
	 * @psalm-pure
	 *
	 * @psalm-return A|TValue
	 * @phpstan-return A|TValue
	 *
	 * @complexity O(N) Lazy.
	 */
	public function foldr1(callable $predicate)
	{
		if ($this->null()) {
			throw new EmptyListException(
				'Empty list.'
			);
		}

		$init = $this->init();
		$last = $this->last();

		return $init->foldr($predicate, $last);
	}

	/**
	 * Take n, applied to a list xs,
	 *     returns the prefix of xs of length n, or xs itself if n > length xs:.
	 *
	 * @psalm-pure
	 *
	 * @psalm-return Listt<TKey, TValue>
	 * @phpstan-return Listt<TKey, TValue>
	 *
	 * @complexity O(N).
	 */
	public function take(int $n, bool $preserveNumericKeys = false): self
	{
		if ($n <= 0) {
			/**
			 * @psalm-var iterable<TKey, TValue>
			 * @phpstan-var iterable<TKey, TValue>
			 */
			$emptyList = [];

			return self::fromIter($emptyList);
		}

		if ($this->null()) {
			return $this;
		}

		/**
		 * @psalm-var callable():\Generator<TKey, TValue>
		 */
		$makeGeneratorFn = function () use (
			$preserveNumericKeys,
			$n
		): \Generator {
			$takenElements = 0;
			foreach ($this->toGenerator() as $k => $v) {
				if ($takenElements === $n) {
					break;
				}

				++$takenElements;

				if (!$preserveNumericKeys && \is_int($k)) {
					yield $v;

					continue;
				}

				yield $k => $v;
			}
		};

		return self::fromGenerator(
			$makeGeneratorFn
		);
	}

	/**
	 * Determines whether any element of the structure satisfies the predicate.
	 *
	 * @psalm-param callable(TValue, TKey=):bool $predicate
	 * @phpstan-param callable(TValue, TKey|mixed):bool $predicate
	 *
	 * @psalm-pure
	 *
	 * @complexity O(N).
	 */
	public function any(
		callable $predicate
	): bool {
		return !$this->select($predicate)->isEmpty();
	}

	/**
	 * Determines whether any element of the structure satisfies the passed element.
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
	public function contains(
		$element
	): bool {
		return !$this->select(
			/**
			 * @psalm-param TValue $value
			 */
			static fn ($value) => $value === $element
		)->isEmpty();
	}

	/**
	 * Determines whether any key of the structure satisfies the passed key.
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
	public function containsKey(
		$key
	): bool {
		return !$this->select(
			/**
			 * @psalm-param TValue $value
			 * @psalm-param TKey $key_
			 */
			static fn ($value, $key_) => $key_ === $key
		)->isEmpty();
	}

	/**
	 * @psalm-pure
	 * @psalm-return \Generator<TKey, TValue>
	 * @phpstan-return \Generator<TKey, TValue>
	 *
	 * @complexity O(1)
	 */
	public function toGenerator(): \Generator
	{
		// Generator may be used internally for `null` method.
		// So, we'll re-use this generator
		//   to avoid redundant iterations.
		$currentGenerator = $this->getGenerator();

		$this->generator = null;

		return $currentGenerator;
	}

	/**
	 * @psalm-pure
	 * @psalm-return array<array-key, TValue>
	 * @phpstan-return array<array-key, TValue>
	 *
	 * @complexity O(N)
	 */
	public function toArray(): array
	{
		/** @psalm-var array<array-key, TValue> */
		return iterator_to_array(
			$this->toGenerator()
		);
	}

	/**
	 * @psalm-pure
	 *
	 * @complexity when source is array
	 *     or count is specified while creating a list,
	 *     then O(1), otherwise O(N).
	 */
	public function count(): int
	{
		if (null === $this->count) {
			if (null !== $this->countFn) {
				$countFn = $this->countFn;
				/** @psalm-suppress InaccessibleProperty */
				$this->count = $countFn();
			} else {
				/** @psalm-suppress InaccessibleProperty */
				$this->count = iterator_count($this);
			}

			if (null === $this->isEmpty) {
				/** @psalm-suppress InaccessibleProperty */
				$this->isEmpty = 0 === $this->count;
			}
		}

		return $this->count;
	}

	/**
	 * @complexity O(1)
	 */
	public function rewind(): void
	{
		/** @psalm-suppress ImpurePropertyAssignment */
		$this->generatorForIterator = $this->toGenerator();
	}

	/**
	 * @complexity O(1)
	 */
	public function valid(): bool
	{
		/** @psalm-suppress ImpureMethodCall */
		return $this->getGeneratorForIterator()->valid();
	}

	/**
	 * @psalm-return TKey
	 * @phpstan-return TKey
	 *
	 * @complexity O(1)
	 */
	public function key()
	{
		/** @psalm-suppress ImpureMethodCall */
		return $this->getGeneratorForIterator()->key();
	}

	/**
	 * @complexity O(1)
	 */
	public function next(): void
	{
		/** @psalm-suppress ImpureMethodCall */
		$this->getGeneratorForIterator()->next();
	}

	/**
	 * @psalm-pure
	 * @psalm-return TValue
	 * @phpstan-return TValue
	 *
	 * @complexity O(1)
	 */
	public function current()
	{
		/** @psalm-suppress ImpureMethodCall */
		return $this->getGeneratorForIterator()->current();
	}

	/**
	 * @return string
	 */
	public function serialize()
	{
		return serialize($this->toArray());
	}

	/**
	 * @param string $serialized
	 */
	public function unserialize($serialized): void
	{
		/**
		 * @psalm-var array<TKey, TValue>
		 * @phpstan-var array<TKey, TValue>
		 */
		$data = unserialize($serialized);
		/**
		 * @psalm-suppress MixedArgumentTypeCoercion
		 */
		$count = \count($data);

		$this->__construct(
			static fn () => self::yieldFromIter($data),
			$count
		);
	}

	/**
	 * @psalm-template XKey
	 * @phpstan-template XKey
	 *
	 * @psalm-template XValue
	 * @phpstan-template XValue
	 *
	 * @psalm-param iterable<XKey, XValue> $value
	 * @phpstan-param iterable<XKey, XValue> $value
	 *
	 * @psalm-pure
	 *
	 * @psalm-return \Generator<XKey, XValue>
	 * @phpstan-return \Generator<XKey, XValue>
	 */
	protected static function yieldFromIter(iterable $value): \Generator
	{
		foreach ($value as $k => $v) {
			yield $k => $v;
		}
	}

	/**
	 * @psalm-template X
	 * @phpstan-template X
	 *
	 * @psalm-param X $a
	 * @phpstan-param X $a
	 * @psalm-param X $a
	 *
	 * @psalm-param X $b
	 * @phpstan-param X $b
	 *
	 * @psalm-return Ord::EQ|Ord::GT|Ord::LT
	 * @phpstan-return int(0)|int(1)|int(-1)
	 *
	 * @param mixed $a
	 * @param mixed $b
	 *
	 * @psalm-pure
	 */
	protected static function compare($a, $b)
	{
		if ($a instanceof Ord && $b instanceof Ord) {
			/**
			 * @psalm-var Ord $a
			 * @psalm-var Ord $b
			 * @psalm-suppress ImpureMethodCall
			 */
			return $a->compare($b);
		}

		if (!is_scalar($a) || !is_scalar($b)) {
			throw new \RuntimeException(
				'Only scalars or instance of `Ord` allowed.',
			);
		}

		if ($a > $b) {
			return Ord::GT;
		}

		if ($a < $b) {
			return Ord::LT;
		}

		return Ord::EQ;
	}

	/**
	 * @param float|int $a
	 * @param float|int $b
	 *
	 * @return float|int
	 */
	private static function _add($a, $b)
	{
		return $a + $b;
	}

	/**
	 * @param float|int $a
	 * @param float|int $b
	 *
	 * @return float|int
	 */
	private static function _multiply($a, $b)
	{
		return $a * $b;
	}

	/**
	 * @psalm-pure
	 *
	 * @psalm-template XKey
	 * @psalm-template XValue
	 *
	 * @psalm-param Listt<XKey, XValue> $list
	 *
	 * @psalm-assert-if-true Listt<XKey, int> $list
	 */
	private static function assertIntList(self $list): bool
	{
		return !$list->null() && \is_int($list->head());
	}

	/**
	 * @psalm-pure
	 *
	 * @psalm-template XKey
	 * @psalm-template XValue
	 *
	 * @psalm-param Listt<XKey, XValue> $list
	 *
	 * @psalm-assert-if-true Listt<XKey, float> $list
	 */
	private static function assertFloatList(self $list): bool
	{
		return !$list->null() && \is_float($list->head());
	}

	/**
	 * @psalm-pure
	 *
	 * @psalm-template XKey
	 * @psalm-template XValue
	 *
	 * @psalm-param Listt<XKey, XValue> $list
	 *
	 * @psalm-assert-if-true Listt<XKey, numeric> $list
	 */
	private static function assertNumericList(self $list): bool
	{
		return !$list->null() && is_numeric($list->head());
	}

	/**
	 * @psalm-pure
	 * @psalm-return \Generator<TKey, TValue>
	 * @phpstan-return \Generator<TKey, TValue>
	 */
	private function getGenerator(): \Generator
	{
		if (null === $this->generator) {
			$makeGeneratorFn = $this->makeGeneratorFn;
			/** @psalm-suppress ImpurePropertyAssignment */
			$this->generator = $makeGeneratorFn();
		}

		return $this->generator;
	}

	/**
	 * @psalm-pure
	 * @psalm-return \Generator<TKey, TValue>
	 * @phpstan-return \Generator<TKey, TValue>
	 */
	private function getGeneratorForIterator(): \Generator
	{
		if (null === $this->generatorForIterator) {
			/** @psalm-suppress ImpurePropertyAssignment */
			$this->generatorForIterator = $this->toGenerator();
		}

		return $this->generatorForIterator;
	}
}
