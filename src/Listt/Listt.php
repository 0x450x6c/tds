<?php declare(strict_types=1);

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
 * @phan-template TKey
 *
 * @psalm-template TValue
 * @phpstan-template TValue
 * @phan-template TValue
 *
 * @template-implements \Iterator<TKey, TValue>
 * @psalm-immutable
 */
class Listt implements \Iterator, \Countable
{
	/**
	 * @psalm-var \Closure():\Generator<TKey, TValue>
	 * @phpstan-var \Closure():\Generator<TKey, TValue>
	 * @phan-var \Closure():(\Generator<TKey, TValue>)
	 *
	 * @psalm-allow-private-mutation
	 */
	private \Closure $makeGeneratorFn;

	/**
	 * @psalm-var null|\Generator<TKey, TValue>
	 * @phpstan-var null|\Generator<TKey, TValue>
	 * @phan-var null|\Generator<TKey, TValue>
	 *
	 * @phan-suppress PhanTypeMismatchProperty
	 *
	 * @psalm-allow-private-mutation
	 */
	private ?\Generator $generator = null;

	/**
	 * @psalm-var null|\Generator<TKey, TValue>
	 * @phpstan-var null|\Generator<TKey, TValue>
	 * @phan-var null|\Generator<TKey, TValue>
	 *
	 * @phan-suppress PhanTypeMismatchProperty
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
	 * @psalm-var null|\Closure():int $count
	 * @phpstan-var null|\Closure():int $count
	 * @phan-var null|\Closure():int $count
	 * @psalm-allow-private-mutation
	 */
	private ?\Closure $countFn = null;

	/**
	 * @psalm-param \Closure():\Generator<TKey, TValue> $makeGeneratorFn
	 * @phpstan-param \Closure():\Generator<TKey, TValue> $makeGeneratorFn
	 * @phan-param \Closure():(\Generator<TKey, TValue>) $makeGeneratorFn
	 *
	 * @psalm-param null|int|\Closure():int $count
	 * @phpstan-param null|int|\Closure():int $count
	 * @phan-param null|int|\Closure():int $count
	 *
	 * @phan-suppress PhanCommentParamOutOfOrder
	 *
	 * @param null|\Closure|int $count
	 *
	 * @psalm-pure
	 */
	private function __construct(
		\Closure $makeGeneratorFn,
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
	 * @psalm-param null|\Closure(TValue=, TKey=) $predicate
	 * @phpstan-param null|\Closure(TValue=, TKey=):(void|mixed) $predicate
	 * @phan-param null|\Closure(TValue=, TKey=):(void|mixed) $predicate
	 *
	 * @psalm-pure
	 *
	 * @Complexity O(N)
	 */
	public function __invoke(?\Closure $predicate = null): void
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
	 * @phan-template XKey
	 *
	 * @psalm-template XValue
	 * @phpstan-template XValue
	 * @phan-template XValue
	 *
	 * @psalm-param iterable<XKey, XValue> $list
	 * @phpstan-param iterable<XKey, XValue> $list
	 * @phan-param iterable<XKey, XValue> $list
	 *
	 * @psalm-return Listt<TKey|XKey, TValue|XValue>
	 * @phpstan-return Listt<TKey|XKey, TValue|XValue>
	 * @phan-return Listt<TKey|XKey, TValue|XValue>
	 *
	 * @Complexity O(N) Lazy.
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
		 * @phpstan-var \Closure():\Generator<TKey|XKey, TValue|XValue>
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
	 * @phan-return TValue
	 *
	 * @Complexity O(1)
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
	 * @psalm-return Maybe<TValue>
	 * @phpstan-return Maybe<TValue>
	 * @phan-return Maybe<TValue>
	 *
	 * @Complexity O(1)
	 */
	public function headMaybe(): Maybe
	{
		if ($this->null()) {
			return nothing();
		}

		return just($this->head());
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
	 * @phan-return TValue
	 *
	 * @Complexity O(N)
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
	 * @phan-return Maybe<TValue>
	 *
	 * @Complexity O(N)
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
	 * @phan-return Listt<TKey, TValue>
	 *
	 * @Complexity O(N)
	 */
	public function tail(bool $preserveNumericKeys = false): self
	{
		if ($this->null()) {
			throw new EmptyListException();
		}

		/**
		 * @phpstan-var \Closure():\Generator<TKey, TValue>
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
	 * @phan-return Listt<TKey, TValue>
	 *
	 * @Complexity O(N)
	 */
	public function init(): self
	{
		if ($this->null()) {
			throw new EmptyListException();
		}

		/**
		 * @phpstan-var \Closure():\Generator<TKey, TValue>
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
	 * @phan-return Maybe<array{TValue, Listt<TKey, TValue>}>
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
	 * @Complexity O(1)
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
	 * @Complexity O(1)
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
	 * @Complexity when source is array
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
	 * @phan-return Maybe<TValue>
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
	 * @phan-template XKey
	 *
	 * @psalm-template XValue
	 * @phpstan-template XValue
	 * @phan-template XValue
	 *
	 * @psalm-pure
	 *
	 * @phan-suppress PhanTemplateTypeNotDeclaredInFunctionParams
	 *
	 * @psalm-return Listt<XKey, XValue>
	 * @phpstan-return Listt<XKey, XValue>
	 * @phan-return Listt<XKey, XValue>
	 *
	 * @Complexity O(1)
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
	 * @phan-template XKey
	 *
	 * @psalm-template XValue
	 * @phpstan-template XValue
	 * @phan-template XValue
	 *
	 * @psalm-param \Closure():\Generator<XKey, XValue> $makeGeneratorFn
	 * @phpstan-param \Closure():\Generator<XKey, XValue> $makeGeneratorFn
	 * @phan-param \Closure():(\Generator<XKey, XValue>) $makeGeneratorFn
	 *
	 * @psalm-param null|int|\Closure():int $count
	 * @phpstan-param null|int|\Closure():int $count
	 * @phan-param null|int|\Closure():int $count
	 *
	 * @param null|\Closure|int $count
	 *
	 * @phan-suppress PhanCommentParamOutOfOrder
	 *
	 * @psalm-pure
	 *
	 * @psalm-return Listt<XKey, XValue>
	 * @phpstan-return Listt<XKey, XValue>
	 * @phan-return Listt<XKey, XValue>
	 *
	 * @Complexity O(1) just creates a list, but not iterates by.
	 */
	public static function fromGenerator(
		\Closure $makeGeneratorFn,
		$count = null
	): self {
		return new self($makeGeneratorFn, $count);
	}

	/**
	 * Creates a list from single element.
	 *
	 * @psalm-template XValue
	 * @phpstan-template XValue
	 * @phan-template XValue
	 *
	 * @psalm-param XValue $value
	 * @phpstan-param XValue $value
	 * @phan-param XValue $value
	 *
	 * @psalm-pure
	 *
	 * @psalm-return Listt<int, XValue>
	 * @phpstan-return Listt<int, XValue>
	 * @phan-return Listt<int, XValue>
	 *
	 * @param mixed $value
	 *
	 * @Complexity O(1)
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
	 * @phan-template XKey
	 *
	 * @psalm-template XValue
	 * @phpstan-template XValue
	 * @phan-template XValue
	 *
	 * @param null|\Closure|int $count
	 *
	 * @psalm-param iterable<XKey, XValue> $value
	 * @psalm-param null|int|\Closure():int $count
	 *
	 * @phpstan-param iterable<XKey, XValue> $value
	 * @phpstan-param null|int|\Closure():int $count
	 *
	 * @phan-param iterable<XKey, XValue> $value
	 * @phan-param null|int|\Closure():int $count
	 *
	 * @phan-suppress PhanCommentParamOutOfOrder
	 *
	 * @psalm-pure
	 *
	 * @psalm-return Listt<XKey, XValue>
	 * @phpstan-return Listt<XKey, XValue>
	 * @phan-return Listt<XKey, XValue>
	 *
	 * @Complexity O(1) just creates a list, but not iterates by.
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

		if ($value instanceof \Generator) {
			throw new \InvalidArgumentException(
				'Use `Listt::fromGenerator` for generators.'
			);
		}

		/** @phan-var iterable<XKey, XValue> $value */

		return self::fromGenerator(
			static fn () => self::yieldFromIter($value),
			$count
		);
	}

	/**
	 * Iterates over list applying predicate (if specified).
	 *
	 * @psalm-param null|\Closure(TValue=, TKey=) $predicate
	 * @phpstan-param null|\Closure(TValue=, TKey=):(void|mixed) $predicate
	 * @phan-param null|\Closure(TValue=, TKey=):(void|mixed) $predicate
	 *
	 * @psalm-pure
	 *
	 * @Complexity O(N)
	 */
	public function apply(?\Closure $predicate = null): void
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
	 * @phan-return TValue
	 *
	 * @Complexity O(N) where N = $n.
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
	 * @psalm-param \Closure(TValue, TKey=):bool $predicate
	 * @phpstan-param (\Closure(TValue):bool)&(\Closure(TValue, TKey):bool) $predicate
	 * @phan-param \Closure(TValue):bool|\Closure(TValue, TKey):bool $predicate
	 *
	 * @psalm-pure
	 *
	 * @psalm-return Listt<TKey, TValue>
	 * @phpstan-return Listt<TKey, TValue>
	 * @phan-return Listt<TKey, TValue>
	 *
	 * @Complexity O(N) Lazy.
	 */
	public function select(
		\Closure $predicate,
		bool $preserveNumericKeys = false
	): self {
		/**
		 * @phpstan-var \Closure():\Generator<TKey, TValue>
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
	 * @phan-suppress PhanTypeMismatchReturnNullable
	 * @phan-suppress PhanPartialTypeMismatchReturn
	 *
	 * @psalm-return TValue
	 * @phpstan-return TValue
	 * @phan-return TValue
	 *
	 * @Complexity O(N)
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
			if ($v instanceof Ord) {
				/** @var Ord $minimum */
				if ($v->compare($minimum) < 0) {
					$minimum = $v;
				}
			} elseif ($v < $minimum) {
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
	 * @phan-suppress PhanTypeMismatchReturnNullable
	 * @phan-suppress PhanPartialTypeMismatchReturn
	 *
	 * @psalm-return TValue
	 * @phpstan-return TValue
	 * @phan-return TValue
	 *
	 * @Complexity O(N)
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
			if ($v instanceof Ord) {
				/** @var Ord $maximum */
				if ($v->compare($maximum) > 0) {
					$maximum = $v;
				}
			} elseif ($v > $maximum) {
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
	 * @Complexity O(N) Lazy.
	 */
	public function tap(\Closure $predicate): self
	{
		/**
		 * @phpstan-var \Closure():\Generator<TKey, TValue>
		 * @phan-suppress PhanParamTooMany
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
	 * @phan-template X
	 *
	 * @psalm-param \Closure(TValue, TKey=):X $predicate
	 * @phpstan-param (\Closure(TValue):X)&(\Closure(TValue, TKey):X) $predicate
	 * @phan-param (\Closure(TValue):X)|(\Closure(TValue, TKey):X) $predicate
	 *
	 * @psalm-pure
	 *
	 * @psalm-return Listt<TKey, X>
	 * @phpstan-return Listt<TKey, X>
	 * @phan-return Listt<TKey, X>
	 *
	 * @Complexity O(N) Lazy.
	 */
	public function map(\Closure $predicate): self
	{
		/**
		 * @phpstan-var \Closure():\Generator<TKey, X>
		 */
		$generator = function () use ($predicate): \Generator {
			foreach ($this->toGenerator() as $k => $v) {
				yield $k => \call_user_func_array($predicate, [$v, $k]);
			}
		};

		return self::fromGenerator(
			$generator,
			$this->count ?? $this->countFn
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
	 * @phan-template X
	 *
	 * @psalm-param \Closure(TValue=, TKey=):Maybe<X> $predicate
	 * @phpstan-param \Closure(TValue=, TKey=):Maybe<X> $predicate
	 * @phan-param \Closure(TValue=, TKey=):(Maybe<X>) $predicate
	 *
	 * @psalm-pure
	 *
	 * @psalm-return Listt<TKey, X>
	 * @phpstan-return Listt<TKey, X>
	 * @phan-return Listt<TKey, X>
	 *
	 * @Complexity O(N) Lazy.
	 *
	 * @phan-suppress PhanTemplateTypeNotUsedInFunctionReturn
	 */
	public function mapMaybe(\Closure $predicate): self
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
	 * @phan-return Listt<TKey, TValue>
	 *
	 * @Complexity O(2N) Creates one array, and reverse iterate.
	 */
	public function reverse(bool $preserveNumericKeys = false): self
	{
		/** @psalm-var \Closure():\Generator<TKey, TValue> */
		$generatorFn = function () use ($preserveNumericKeys): \Generator {
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
			$generatorFn,
			$count
		);
	}

	/**
	 * @psalm-pure
	 * @psalm-return \Generator<TKey, TValue>
	 * @phpstan-return \Generator<TKey, TValue>
	 * @phan-return \Generator<TKey, TValue>
	 *
	 * @Complexity O(1)
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
	 * @phan-return array<string|int, TValue>
	 *
	 * @Complexity O(N)
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
	 * @Complexity when source is array
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
	 * @Complexity O(1)
	 */
	public function rewind(): void
	{
		/** @psalm-suppress ImpurePropertyAssignment */
		$this->generatorForIterator = $this->toGenerator();
	}

	/**
	 * @Complexity O(1)
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
	 * @Complexity O(1)
	 */
	public function key()
	{
		/** @psalm-suppress ImpureMethodCall */
		return $this->getGeneratorForIterator()->key();
	}

	/**
	 * @Complexity O(1)
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
	 * @phan-return TValue
	 *
	 * @Complexity O(1)
	 */
	public function current()
	{
		/** @psalm-suppress ImpureMethodCall */
		return $this->getGeneratorForIterator()->current();
	}

	/**
	 * @psalm-pure
	 * @psalm-return \Generator<TKey, TValue>
	 * @phpstan-return \Generator<TKey, TValue>
	 * @phan-return \Generator<TKey, TValue>
	 *
	 * @phan-suppress PhanPartialTypeMismatchReturn
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
	 * @phan-return \Generator<TKey, TValue>
	 *
	 * @phan-suppress PhanPartialTypeMismatchReturn
	 */
	private function getGeneratorForIterator(): \Generator
	{
		if (null === $this->generatorForIterator) {
			/** @psalm-suppress ImpurePropertyAssignment */
			$this->generatorForIterator = $this->toGenerator();
		}

		return $this->generatorForIterator;
	}

	/**
	 * @psalm-template XKey
	 * @phpstan-template XKey
	 * @phan-template XKey
	 *
	 * @psalm-template XValue
	 * @phpstan-template XValue
	 * @phan-template XValue
	 *
	 * @psalm-param iterable<XKey, XValue> $value
	 * @phpstan-param iterable<XKey, XValue> $value
	 * @phan-param iterable<XKey, XValue> $value
	 *
	 * @psalm-pure
	 *
	 * @psalm-return \Generator<XKey, XValue>
	 * @phpstan-return \Generator<XKey, XValue>
	 * @phan-return \Generator<XKey, XValue>
	 */
	private static function yieldFromIter(iterable $value): \Generator
	{
		foreach ($value as $k => $v) {
			yield $k => $v;
		}
	}
}
