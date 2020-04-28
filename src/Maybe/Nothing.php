<?php declare(strict_types=1);

namespace TDS\Maybe;

use TDS\Listt\Listt;

/**
 * @template-extends Maybe<mixed>
 *
 * @psalm-immutable
 */
final class Nothing extends Maybe
{
	/**
	 * @psalm-readonly
	 */
	private static ?self $instance = null;

	/**
	 * @phan-suppress PhanGenericConstructorTypes
	 *
	 * @psalm-pure
	 */
	private function __construct()
	{
	}

	/**
	 * Alias for Maybe::apply().
	 *
	 * @psalm-param \Closure $predicate
	 * @phpstan-param \Closure $predicate
	 * @phan-param \Closure $predicate
	 *
	 * @phan-suppress PhanUnusedPublicFinalMethodParameter
	 *
	 * @psalm-pure
	 */
	public function __invoke(\Closure $predicate): void
	{
	}

	/**
	 * @psalm-pure
	 *
	 * @phan-suppress PhanPartialTypeMismatchReturn
	 */
	public static function instance(): self
	{
		/** @psalm-suppress ImpureStaticProperty */
		if (null === self::$instance) {
			self::$instance = new self();
		}

		/** @psalm-suppress ImpureStaticProperty */
		return self::$instance;
	}

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
	 * @psalm-param X $defaultValue
	 * @phpstan-param X $defaultValue
	 * @phan-param X $defaultValue
	 *
	 * @psalm-param \Closure $predicate
	 * @phpstan-param \Closure $predicate
	 * @phan-param \Closure $predicate
	 *
	 * @psalm-return X
	 * @phpstan-return X
	 * @phan-return X
	 *
	 * @phan-suppress PhanTemplateTypeNotUsedInFunctionReturn
	 * @phan-suppress PhanUnusedPublicFinalMethodParameter
	 *
	 * @psalm-pure
	 *
	 * @param mixed $defaultValue
	 */
	public function maybe($defaultValue, \Closure $predicate)
	{
		return $defaultValue;
	}

	/**
	 * @psalm-pure
	 */
	public function isJust(): bool
	{
		return false;
	}

	/**
	 * @psalm-pure
	 */
	public function isNothing(): bool
	{
		return true;
	}

	/**
	 * @psalm-pure
	 *
	 * @throws UsingFromJustOnNothingException
	 */
	public function fromJust(): void
	{
		throw new UsingFromJustOnNothingException();
	}

	/**
	 * @psalm-template X
	 * @phpstan-template X
	 * @phan-template X
	 *
	 * @psalm-param X $defaultValue
	 * @phpstan-param X $defaultValue
	 * @phan-param X $defaultValue
	 *
	 * @psalm-return X
	 * @phpstan-return X
	 * @phan-return X
	 *
	 * @psalm-pure
	 *
	 * @param mixed $defaultValue
	 */
	public function fromMaybe($defaultValue)
	{
		return $defaultValue;
	}

	/**
	 * @psalm-return Listt<int, mixed>
	 * @phpstan-return Listt<int, mixed>
	 * @phan-return Listt<int, mixed>
	 * @phan-suppress PhanParamSignatureMismatch
	 */
	public function toList(): Listt
	{
		/**
		 * @psalm-var iterable<int, mixed>
		 * @phpstan-var iterable<int, mixed>
		 * @phan-var iterable<int, mixed>
		 */
		$list = [];

		return Listt::fromIter($list);
	}

	/**
	 * Apply predicate if `Just`.
	 *
	 * @psalm-param \Closure $predicate
	 * @phpstan-param \Closure $predicate
	 * @phan-param \Closure $predicate
	 *
	 * @phan-suppress PhanUnusedPublicFinalMethodParameter
	 *
	 * @psalm-pure
	 */
	public function apply(\Closure $predicate): void
	{
	}

	public function rewind(): void
	{
	}

	public function valid(): bool
	{
		return false;
	}

	public function key(): int
	{
		throw new \RuntimeException('Iterator is not valid.');
	}

	public function next(): void
	{
	}

	/**
	 * @psalm-pure
	 */
	public function current(): void
	{
		throw new \RuntimeException('Iterator is not valid.');
	}

	/**
	 * @psalm-pure
	 */
	public function count(): int
	{
		return 0;
	}
}
