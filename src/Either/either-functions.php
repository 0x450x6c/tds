<?php

declare(strict_types=1);

namespace TDS\Either;

use TDS\Listt\Listt;
use function TDS\Maybe\mapMaybe;
use TDS\Maybe\Maybe;

/**
 * @psalm-template L
 * @phpstan-template L
 *
 * @psalm-param L $value
 * @phpstan-param L $value
 *
 * @psalm-return Left<L>
 * @phpstan-return Left<L>
 *
 * @param mixed $value
 */
function left($value): Left
{
	return Left::new($value);
}

/**
 * @psalm-template R
 * @phpstan-template R
 *
 * @psalm-param R $value
 * @phpstan-param R $value
 *
 * @psalm-return Right<R>
 * @phpstan-return Right<R>
 *
 * @param mixed $value
 */
function right($value): Right
{
	return Right::new($value);
}

/**
 * Case analysis for the Either type.
 *
 * If the value is Left a, apply the first function to a;
 *   if it is Right b, apply the second function to b.
 *
 * @psalm-template L
 * @phpstan-template L
 *
 * @psalm-template R
 * @phpstan-template R
 *
 * @psalm-template LReturn
 * @phpstan-template LReturn
 *
 * @psalm-template RReturn
 * @phpstan-template RReturn
 *
 * @psalm-param Either<L, R> $either
 * @phpstan-param Either<L, R> $either
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
function either(
	Either $either,
	callable $leftPredicate,
	callable $rightPredicate
) {
	return $either->either($leftPredicate, $rightPredicate);
}

/**
 * @psalm-template L
 * @phpstan-template L
 *
 * @psalm-template R
 * @phpstan-template R
 *
 * @psalm-param Either<L, R> $either
 * @phpstan-param Either<L, R> $either
 *
 * @psalm-assert-if-true Left<L, R> $either
 * @psalm-assert-if-false Right<L, R> $either
 *
 * @psalm-pure
 */
function isLeft(Either $either): bool
{
	return $either->isLeft();
}

/**
 * @psalm-template L
 * @phpstan-template L
 *
 * @psalm-template R
 * @phpstan-template R
 *
 * @psalm-param Either<L, R> $either
 * @phpstan-param Either<L, R> $either
 *
 * @psalm-assert-if-true Right<L, R> $either
 * @psalm-assert-if-false Left<L, R> $either
 *
 * @psalm-pure
 */
function isRight(Either $either): bool
{
	return $either->isRight();
}

/**
 * @psalm-template L
 * @phpstan-template L
 *
 * @psalm-template R
 * @phpstan-template R
 *
 * @psalm-template D
 * @phpstan-template D
 *
 * @psalm-param Either<L, R> $either
 * @phpstan-param Either<L, R> $either
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
function fromLeft(Either $either, $defaultValue)
{
	return $either->fromLeft($defaultValue);
}

/**
 * @psalm-template L
 * @phpstan-template L
 *
 * @psalm-template R
 * @phpstan-template R
 *
 * @psalm-template D
 * @phpstan-template D
 *
 * @psalm-param Either<L, R> $either
 * @phpstan-param Either<L, R> $either
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
function fromRight(Either $either, $defaultValue)
{
	return $either->fromRight($defaultValue);
}

/**
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template L
 * @phpstan-template L
 *
 * @psalm-template R
 * @phpstan-template R
 *
 * @psalm-param iterable<TKey, Either<L, R>> $eithers
 * @phpstan-param iterable<TKey, Either<L, R>> $eithers
 *
 * @psalm-return Listt<TKey, L>
 * @phpstan-return Listt<TKey, L>
 */
function lefts(iterable $eithers): Listt
{
	/**
	 * @phpstan-var callable(Either<L, R>):Maybe<L>
	 */
	$predicate = static function (Either $either): Maybe {
		return $either->maybeLeft();
	};

	return mapMaybe(
		$eithers,
		$predicate
	);
}

/**
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template L
 * @phpstan-template L
 *
 * @psalm-template R
 * @phpstan-template R
 *
 * @psalm-param iterable<TKey, Either<L, R>> $eithers
 * @phpstan-param iterable<TKey, Either<L, R>> $eithers
 *
 * @psalm-return Listt<TKey, R>
 * @phpstan-return Listt<TKey, R>
 */
function rights(iterable $eithers): Listt
{
	/**
	 * @phpstan-var callable(Either<L, R>):Maybe<R>
	 */
	$predicate = static function (Either $either): Maybe {
		return $either->maybeRight();
	};

	return mapMaybe(
		$eithers,
		$predicate
	);
}

/**
 * @psalm-template TKey
 * @phpstan-template TKey
 *
 * @psalm-template L
 * @phpstan-template L
 *
 * @psalm-template R
 * @phpstan-template R
 *
 * @psalm-param iterable<TKey, Either<L, R>> $eithers
 * @phpstan-param iterable<TKey, Either<L, R>> $eithers
 *
 * @psalm-return array{Listt<TKey, L>, Listt<TKey, R>}
 * @phpstan-return array{Listt<TKey, L>, Listt<TKey, R>}
 */
function partitionEithers(iterable $eithers): array
{
	return [
		lefts($eithers),
		rights($eithers),
	];
}
