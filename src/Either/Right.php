<?php declare(strict_types=1);

namespace TDS\Either;

use TDS\Maybe\Just;
use function TDS\Maybe\just;
use TDS\Maybe\Nothing;
use function TDS\Maybe\nothing;

/**
 * @psalm-template R
 *
 * @template-implements Either<mixed, R>
 *
 * @psalm-immutable
 */
class Right implements Either
{
	/**
	 * @psalm-var R
	 */
	private $value;

	/**
	 * @psalm-param R $value
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
	 * @psalm-template RReturn
	 *
	 * @psalm-param callable $leftPredicate
	 *
	 * @psalm-param callable(R):RReturn $rightPredicate
	 *
	 * @psalm-return RReturn
	 *
	 * @psalm-pure
	 */
	public function either(callable $leftPredicate, callable $rightPredicate)
	{
		return $rightPredicate($this->value);
	}

	/**
	 * @psalm-pure
	 */
	public function isLeft(): bool
	{
		return false;
	}

	/**
	 * @psalm-pure
	 */
	public function isRight(): bool
	{
		return true;
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
	public function fromLeft($defaultValue)
	{
		return $defaultValue;
	}

	/**
	 * @psalm-template D
	 *
	 * @psalm-param D $defaultValue
	 *
	 * @psalm-return R
	 *
	 * @psalm-pure
	 *
	 * @param mixed $defaultValue
	 */
	public function fromRight($defaultValue)
	{
		return $this->value;
	}

	/**
	 * @psalm-return Nothing
	 *
	 * @psalm-pure
	 */
	public function maybeLeft(): Nothing
	{
		return nothing();
	}

	/**
	 * @psalm-return Just<R>
	 *
	 * @psalm-pure
	 */
	public function maybeRight(): Just
	{
		return just($this->value);
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
		 * @psalm-var R
		 */
		$data = unserialize($serialized);

		$this->__construct($data);
	}
}
