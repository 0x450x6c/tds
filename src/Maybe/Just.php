<?php declare(strict_types=1);

namespace TDS\Maybe;

use TDS\Listt\Listt;

/**
 * @psalm-template T
 * @phpstan-template T
 * @phan-template T
 *
 * @template-extends Maybe<T>
 *
 * @psalm-immutable
 */
final class Just extends Maybe
{
	/**
	 * @var T
	 *
	 * @psalm-readonly
	 */
	private $value;

	/**
	 * @psalm-readonly-allow-private-mutation
	 *
	 * @var int
	 */
	private $iteratorCursor = 0;

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
	 * Alias for Maybe::apply().
	 *
	 * @psalm-param \Closure(T) $predicate
	 * @phpstan-param \Closure(T):(void|mixed) $predicate
	 * @phan-param \Closure(T):(void|mixed) $predicate
	 *
	 * @psalm-pure
	 */
	public function __invoke(\Closure $predicate): void
	{
		$this->apply($predicate);
	}

	/**
	 * @psalm-template X
	 * @phpstan-template X
	 * @phan-template X
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
	 * @psalm-template X
	 * @phpstan-template X
	 * @phan-template X
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

	/**
	 * Apply predicate if `Just`.
	 *
	 * @psalm-param \Closure(T) $predicate
	 * @phpstan-param \Closure(T):(void|mixed) $predicate
	 * @phan-param \Closure(T):(void|mixed) $predicate
	 *
	 * @psalm-pure
	 */
	public function apply(\Closure $predicate): void
	{
		\call_user_func_array($predicate, [$this->value]);
	}

	public function rewind(): void
	{
		$this->iteratorCursor = 0;
	}

	public function valid(): bool
	{
		return 0 === $this->iteratorCursor;
	}

	public function key(): int
	{
		if (!$this->valid()) {
			throw new \RuntimeException('Iterator is not valid.');
		}

		/** @psalm-var int */
		return $this->iteratorCursor;
	}

	public function next(): void
	{
		/**
		 * @psalm-suppress MixedOperand
		 * @psalm-suppress ImpurePropertyAssignment
		 */
		++$this->iteratorCursor;
	}

	/**
	 * @psalm-pure
	 *
	 * @psalm-return T
	 * @phpstan-return T
	 * @phan-return T
	 */
	public function current()
	{
		if (!$this->valid()) {
			throw new \RuntimeException('Iterator is not valid.');
		}

		return $this->value;
	}

	/**
	 * @psalm-pure
	 */
	public function count(): int
	{
		return 1;
	}

	/**
	 * @return string
	 */
	public function serialize()
	{
		return serialize($this->value);
	}

	/**
	 * @param string $serialized
	 */
	public function unserialize($serialized): void
	{
		/**
		 * @psalm-var T
		 * @phpstan-var T
		 * @phan-var T
		 */
		$value = unserialize($serialized);

		$this->__construct(
			$value
		);
	}
}
