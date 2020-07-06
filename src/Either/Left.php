<?php declare(strict_types=1);

namespace TDS\Either;

use TDS\Maybe\Just;
use function TDS\Maybe\just;
use TDS\Maybe\Nothing;
use function TDS\Maybe\nothing;

/**
 * @psalm-template L
 *
 * @template-implements Either<L, empty>
 *
 * @psalm-immutable
 */
class Left implements Either
{
	/**
	 * @psalm-var L
	 */
	private $value;

	/**
	 * @psalm-param L $value
	 *
	 * @param mixed $value
	 */
	private function __construct(
		$value
	) {
		$this->value = $value;
	}

	/**
	 * @psalm-template X
	 *
	 * @psalm-param X $value
	 *
	 * @psalm-return self<X>
	 *
	 * @param mixed $value
	 */
	public static function new(
		$value
	): self {
		return new self(
			$value
		);
	}

	/**
	 * Case analysis for the Either type.
	 *
	 * If the value is Left a, apply the first function to a;
	 *   if it is Right b, apply the second function to b.
	 *
	 * @psalm-template LReturn
	 *
	 * @psalm-param callable(L):LReturn $leftPredicate
	 *
	 * @psalm-param callable $rightPredicate
	 *
	 * @psalm-return LReturn
	 *
	 * @psalm-pure
	 */
	public function either(callable $leftPredicate, callable $rightPredicate)
	{
		return $leftPredicate($this->value);
	}

	/**
	 * @psalm-pure
	 */
	public function isLeft(): bool
	{
		return true;
	}

	/**
	 * @psalm-pure
	 */
	public function isRight(): bool
	{
		return false;
	}

	/**
	 * @psalm-template D
	 *
	 * @psalm-param D $defaultValue
	 *
	 * @psalm-return L|D
	 *
	 * @psalm-pure
	 *
	 * @param mixed $defaultValue
	 */
	public function fromLeft($defaultValue)
	{
		return $this->value;
	}

	/**
	 * @psalm-template D
	 *
	 * @psalm-param D $defaultValue
	 *
	 * @psalm-return D
	 *
	 * @psalm-pure
	 *
	 * @param mixed $defaultValue
	 */
	public function fromRight($defaultValue)
	{
		return $defaultValue;
	}

	/**
	 * @psalm-return Just<L>
	 *
	 * @psalm-pure
	 */
	public function maybeLeft(): Just
	{
		return just($this->value);
	}

	/**
	 * @psalm-return Nothing
	 *
	 * @psalm-pure
	 */
	public function maybeRight(): Nothing
	{
		return nothing();
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
		 * @psalm-var L
		 */
		$data = unserialize($serialized);

		$this->__construct($data);
	}
}
