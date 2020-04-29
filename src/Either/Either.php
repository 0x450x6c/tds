<?php declare(strict_types=1);

namespace TDS\Either;

use TDS\Maybe\Maybe;

/**
 * Clone of http://hackage.haskell.org/package/base-4.12.0.0/docs/Data-Either.html#v:Either.
 *
 * The Either type, and associated operations.
 *
 * @psalm-template L
 * @phpstan-template L
 * @phan-template L
 *
 * @psalm-template R
 * @phpstan-template R
 * @phan-template R
 *
 * @psalm-immutable
 */
interface Either extends \Serializable
{
	/**
	 * Case analysis for the Either type.
	 *
	 * If the value is Left a, apply the first function to a;
	 *   if it is Right b, apply the second function to b.
	 *
	 * @psalm-template LReturn
	 * @phpstan-template LReturn
	 * @phan-template LReturn
	 *
	 * @psalm-template RReturn
	 * @phpstan-template RReturn
	 * @phan-template RReturn
	 *
	 * @psalm-param \Closure(L):LReturn $leftPredicate
	 * @phpstan-param \Closure(L):LReturn $leftPredicate
	 * @phan-param \Closure(L):LReturn $leftPredicate
	 *
	 * @psalm-param \Closure(R):RReturn $rightPredicate
	 * @phpstan-param \Closure(R):RReturn $rightPredicate
	 * @phan-param \Closure(R):RReturn $rightPredicate
	 *
	 * @psalm-return LReturn|RReturn
	 * @phpstan-return LReturn|RReturn
	 * @phan-return LReturn|RReturn
	 *
	 * @psalm-pure
	 */
	public function either(\Closure $leftPredicate, \Closure $rightPredicate);

	/**
	 * @psalm-pure
	 */
	public function isLeft(): bool;

	/**
	 * @psalm-pure
	 */
	public function isRight(): bool;

	/**
	 * @psalm-template D
	 * @phpstan-template D
	 * @phan-template D
	 *
	 * @psalm-param D $defaultValue
	 * @phpstan-param D $defaultValue
	 * @phan-param D $defeault
	 *
	 * @psalm-return L|D
	 * @phpstan-return L|D
	 * @phan-return L|D
	 *
	 * @psalm-pure
	 *
	 * @param mixed $defaultValue
	 *
	 * @phan-suppress PhanCommentParamWithoutRealParam
	 * @phan-suppress PhanTemplateTypeNotDeclaredInFunctionParams
	 */
	public function fromLeft($defaultValue);

	/**
	 * @psalm-template D
	 * @phpstan-template D
	 * @phan-template D
	 *
	 * @psalm-param D $defaultValue
	 * @phpstan-param D $defaultValue
	 * @phan-param D $defeault
	 *
	 * @psalm-return R|D
	 * @phpstan-return R|D
	 * @phan-return R|D
	 *
	 * @psalm-pure
	 *
	 * @param mixed $defaultValue
	 *
	 * @phan-suppress PhanCommentParamWithoutRealParam
	 * @phan-suppress PhanTemplateTypeNotDeclaredInFunctionParams
	 */
	public function fromRight($defaultValue);

	/**
	 * @psalm-return Maybe<L>
	 * @phpstan-return Maybe<L>
	 * @phan-return Maybe<L>
	 *
	 * @psalm-pure
	 */
	public function maybeLeft(): Maybe;

	/**
	 * @psalm-return Maybe<R>
	 * @phpstan-return Maybe<R>
	 * @phan-return Maybe<R>
	 *
	 * @psalm-pure
	 */
	public function maybeRight(): Maybe;
}
