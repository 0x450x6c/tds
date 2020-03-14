<?php declare(strict_types=1);

namespace TDS\Maybe;

use TDS\Listt\Listt;

/**
 * @phpstan-template T
 * @phan-template T of mixed
 * @psalm-template T of mixed
 *
 * @implements Maybe<T>
 *
 * @psalm-immutable
 */
final class Nothing implements Maybe
{
	/**
	 * @psalm-var null|self<mixed>
	 * @phpstan-var null|self<T>
	 * @phan-var null|self<mixed>
	 *
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
	 * @psalm-return self<mixed>
	 * @phpstan-return self<T>
	 * @phan-return self<T>
	 *
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
	 * @template X
	 * @template Y
	 *
	 * @psalm-param X $defaultValue
	 * @phpstan-param X $defaultValue
	 * @phan-param X $defaultValue
	 *
	 * @psalm-param \Closure(T=):Y $predicate
	 * @phpstan-param \Closure(T=):Y $predicate
	 * @phan-param \Closure(T):Y|\Closure():Y $predicate
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
	 * @throws FromJustNothingException
	 */
	public function fromJust(): void
	{
		throw new FromJustNothingException();
	}

	/**
	 * @template X
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
	 * @psalm-return Listt<int, T>
	 * @phpstan-return Listt<int, T>
	 * @phan-return Listt<int, T>
	 */
	public function toList(): Listt
	{
		/**
		 * @psalm-var iterable<int, T>
		 * @phpstan-var iterable<int, T>
		 * @phan-var iterable<int, T>
		 */
		$list = [];

		return Listt::fromIter($list);
	}
}