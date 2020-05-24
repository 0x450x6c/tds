<?php declare(strict_types=1);

namespace TDS\Maybe;

use TDS\Listt\Listt;

/**
 * @psalm-template T
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
	 * @psalm-param T $value
	 *
	 * @param mixed $value
	 *
	 * @psalm-pure
	 */
	private function __construct($value)
	{
		$this->value = $value;

		parent::__construct(
			static fn () => static::yieldFromIter([$value]),
			1
		);
	}

	/**
	 * @psalm-template X
	 *
	 * @psalm-param X $value
	 *
	 * @psalm-return self<X>
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
	 *
	 * @psalm-template Y
	 *
	 * @psalm-param X $defaultValue
	 *
	 * @psalm-param callable(T):Y $predicate
	 *
	 * @psalm-return Y
	 *
	 * @psalm-pure
	 *
	 * @param mixed $defaultValue
	 */
	public function maybe($defaultValue, callable $predicate)
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
	 *
	 * @psalm-pure
	 */
	public function fromJust()
	{
		return $this->value;
	}

	/**
	 * @psalm-template X
	 *
	 * @psalm-param X $defaultValue
	 *
	 * @psalm-return T
	 *
	 * @psalm-pure
	 *
	 * @param mixed $defaultValue
	 */
	public function fromMaybe($defaultValue)
	{
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
		 */
		$value = unserialize($serialized);

		$this->__construct(
			$value
		);
	}

	/**
	 * @psalm-return Listt<int, T>
	 */
	public function toList(): Listt
	{
		return Listt::fromIter([$this->value]);
	}
}
