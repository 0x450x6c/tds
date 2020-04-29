<?php declare(strict_types=1);

namespace TDS\Either;

use TDS\Maybe\Just;
use function TDS\Maybe\just;
use TDS\Maybe\Nothing;
use function TDS\Maybe\nothing;

/**
 * @psalm-template R
 * @phpstan-template R
 * @phan-template R
 *
 * @template-implements Either<mixed, R>
 *
 * @psalm-immutable
 */
class Right implements Either
{
	/**
	 * @psalm-var R
	 * @phpstan-var R
	 * @phan-var R
	 */
	private $value;

	/**
	 * @psalm-param R $value
	 * @phpstan-param R $value
	 * @phan-param R $value
	 *
	 * @param mixed $value
	 *
	 * @phan-suppress PhanGenericConstructorTypes
	 */
	private function __construct(
		$value
	) {
		$this->value = $value;
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
	 * @phan-return self<X>
	 *
	 * @param mixed $value
	 *
	 * @phan-suppress PhanTemplateTypeNotUsedInFunctionReturn
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
	 * @phpstan-template RReturn
	 * @phan-template RReturn
	 *
	 * @psalm-param \Closure $leftPredicate
	 * @phpstan-param \Closure $leftPredicate
	 * @phan-param \Closure $leftPredicate
	 *
	 * @psalm-param \Closure(R):RReturn $rightPredicate
	 * @phpstan-param \Closure(R):RReturn $rightPredicate
	 * @phan-param \Closure(R):RReturn $rightPredicate
	 *
	 * @psalm-return RReturn
	 * @phpstan-return RReturn
	 * @phan-return RReturn
	 *
	 * @psalm-pure
	 *
	 * @phan-suppress PhanTemplateTypeNotUsedInFunctionReturn
	 * @phan-suppress PhanTemplateTypeNotDeclaredInFunctionParams
	 * @phan-suppress PhanUnusedPublicMethodParameter
	 */
	public function either(\Closure $leftPredicate, \Closure $rightPredicate)
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
	 * @phpstan-template D
	 * @phan-template D
	 *
	 * @psalm-param D $defaultValue
	 * @phpstan-param D $defaultValue
	 * @phan-param D $defaultValue
	 *
	 * @psalm-return D
	 * @phpstan-return D
	 * @phan-return D
	 *
	 * @psalm-pure
	 *
	 * @param mixed $defaultValue
	 *
	 * @phan-suppress PhanCommentParamWithoutRealParam
	 * @phan-suppress PhanTemplateTypeNotDeclaredInFunctionParams
	 */
	public function fromLeft($defaultValue)
	{
		return $defaultValue;
	}

	/**
	 * @psalm-template D
	 * @phpstan-template D
	 * @phan-template D
	 *
	 * @psalm-param D $defaultValue
	 * @phpstan-param D $defaultValue
	 * @phan-param D $defaultValue
	 *
	 * @psalm-return R
	 * @phpstan-return R
	 * @phan-return R
	 *
	 * @psalm-pure
	 *
	 * @param mixed $defaultValue
	 *
	 * @phan-suppress PhanCommentParamWithoutRealParam
	 * @phan-suppress PhanTemplateTypeNotDeclaredInFunctionParams
	 * @phan-suppress PhanUnusedPublicMethodParameter
	 * @phan-suppress PhanTemplateTypeNotUsedInFunctionReturn
	 */
	public function fromRight($defaultValue)
	{
		return $this->value;
	}

	/**
	 * @psalm-return Nothing
	 * @phpstan-return Nothing
	 * @phan-return Nothing
	 *
	 * @psalm-pure
	 *
	 * @phan-suppress PhanParamSignatureMismatch
	 * @phan-suppress PhanParamSignatureRealMismatchReturnType
	 */
	public function maybeLeft(): Nothing
	{
		return nothing();
	}

	/**
	 * @psalm-return Just<R>
	 * @phpstan-return Just<R>
	 * @phan-return Just<R>
	 *
	 * @psalm-pure
	 *
	 * @phan-suppress PhanParamSignatureMismatch
	 * @phan-suppress PhanParamSignatureRealMismatchReturnType
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
		 * @phpstan-var R
		 * @phan-var R
		 */
		$data = unserialize($serialized);

		$this->__construct($data);
	}
}
