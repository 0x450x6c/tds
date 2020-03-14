<?php declare(strict_types=1);

namespace TDS\Maybe;

use TDS\Listt\Listt;

/**
 * @template T
 * @implements Maybe<T>
 *
 * @psalm-immutable
 */
final class Just implements Maybe
{
	/**
	 * @var T
	 *
	 * @readonly
	 */
	private $value;

	/**
	 * @psalm-param T $value
	 * @phpstan-param T $value
	 * @phan-param T $value
	 *
	 * @param mixed $value
	 *
	 * @psalm-pure
	 */
	private function __construct($value)
	{
		$this->value = $value;
	}

	/**
	 * @template X
	 *
	 * @psalm-param X $value
	 * @phpstan-param X $value
	 * @phan-param X $value
	 *
	 * @psalm-return self<X>
	 * @phpstan-return self<X>
	 * @phan-return Just<X>
	 *
	 * @psalm-pure
	 *
	 * @param mixed $value
	 */
	public static function new($value): self
	{
		return new self($value);
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
	 * @phpstan-param \Closure(T):Y $predicate
	 * @phan-param \Closure(T):Y|\Closure():Y $predicate
	 *
	 * @psalm-return Y
	 * @phpstan-return Y
	 * @phan-return Y
	 *
	 * @phan-suppress PhanTemplateTypeNotUsedInFunctionReturn
	 * @phan-suppress PhanUnusedPublicFinalMethodParameter
	 * @phan-suppress PhanParamTooMany
	 *
	 * @psalm-pure
	 *
	 * @param mixed $defaultValue
	 */
	public function maybe($defaultValue, \Closure $predicate)
	{
		return $predicate($this->value);
	}

	/**
	 * @psalm-pure
	 */
	public function isJust(): bool
	{
		return true;
	}

	/**
	 * @psalm-pure
	 */
	public function isNothing(): bool
	{
		return false;
	}

	/**
	 * @psalm-return T
	 * @phpstan-return T
	 * @phan-return T
	 *
	 * @psalm-pure
	 */
	public function fromJust()
	{
		return $this->value;
	}

	/**
	 * @template X
	 *
	 * @psalm-param X $defaultValue
	 * @phpstan-param X $defaultValue
	 * @phan-param X $defaultValue
	 *
	 * @psalm-return T
	 * @phpstan-return T
	 * @phan-return T
	 *
	 * @psalm-pure
	 *
	 * @phan-suppress PhanTemplateTypeNotUsedInFunctionReturn
	 * @phan-suppress PhanUnusedPublicFinalMethodParameter
	 *
	 * @param mixed $defaultValue
	 */
	public function fromMaybe($defaultValue)
	{
		return $this->value;
	}

	/**
	 * @psalm-return Listt<int, T>
	 * @phpstan-return Listt<int, T>
	 * @phan-return Listt<int, T>
	 */
	public function toList(): Listt
	{
		return Listt::fromIter([$this->value]);
	}
}
