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
 *
 * @psalm-template R
 * @phpstan-template R
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
	 *
	 * @psalm-template RReturn
	 * @phpstan-template RReturn
	 *
	 * @psalm-param callable(L):LReturn $leftPredicate
	 * @phpstan-param callable(L):LReturn $leftPredicate
	 *
	 * @psalm-param callable(R):RReturn $rightPredicate
	 * @phpstan-param callable(R):RReturn $rightPredicate
	 *
	 * @psalm-return LReturn|RReturn
	 * @phpstan-return LReturn|RReturn
	 *
	 * @psalm-pure
	 */
	public function either(callable $leftPredicate, callable $rightPredicate);

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
	 *
	 * @psalm-param D $defaultValue
	 * @phpstan-param D $defaultValue
	 *
	 * @psalm-return L|D
	 * @phpstan-return L|D
	 *
	 * @psalm-pure
	 *
	 * @param mixed $defaultValue
	 */
	public function fromLeft($defaultValue);

	/**
	 * @psalm-template D
	 * @phpstan-template D
	 *
	 * @psalm-param D $defaultValue
	 * @phpstan-param D $defaultValue
	 *
	 * @psalm-return R|D
	 * @phpstan-return R|D
	 *
	 * @psalm-pure
	 *
	 * @param mixed $defaultValue
	 */
	public function fromRight($defaultValue);

	/**
	 * @psalm-return Maybe<L>
	 * @phpstan-return Maybe<L>
	 *
	 * @psalm-pure
	 */
	public function maybeLeft(): Maybe;

	/**
	 * @psalm-return Maybe<R>
	 * @phpstan-return Maybe<R>
	 *
	 * @psalm-pure
	 */
	public function maybeRight(): Maybe;
}
