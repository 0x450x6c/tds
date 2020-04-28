<?php

declare(strict_types=1);

namespace TDS\Either;

use TDS\Listt\Listt;
use function TDS\Maybe\mapMaybe;
use TDS\Maybe\Maybe;

/**
 * @psalm-template L
 * @phpstan-template L
 * @phan-template L
 *
 * @psalm-param L $value
 * @phpstan-param L $value
 * @phan-param L $value
 *
 * @psalm-return Left<L>
 * @phpstan-return Left<L>
 * @phan-return Left<L>
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
 * @phan-template R
 *
 * @psalm-param R $value
 * @phpstan-param R $value
 * @phan-param R $value
 *
 * @psalm-return Right<R>
 * @phpstan-return Right<R>
 * @phan-return Right<R>
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
 * @phan-template L
 *
 * @psalm-template R
 * @phpstan-template R
 * @phan-template R
 *
 * @psalm-template LReturn
 * @phpstan-template LReturn
 * @phan-template LReturn
 *
 * @psalm-template RReturn
 * @phpstan-template RReturn
 * @phan-template RReturn
 *
 * @psalm-param Either<L, R> $either
 * @phpstan-param Either<L, R> $either
 * @phan-param Either<L, R> $either
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
 *
 * @phan-suppress PhanTemplateTypeNotUsedInFunctionReturn
 */
function either(
	Either $either,
	\Closure $leftPredicate,
	\Closure $rightPredicate
) {
	return $either->either($leftPredicate, $rightPredicate);
}

/**
 * @psalm-template L
 * @phpstan-template L
 * @phan-template L
 *
 * @psalm-template R
 * @phpstan-template R
 * @phan-template R
 *
 * @psalm-param Either<L, R> $either
 * @phpstan-param Either<L, R> $either
 * @phan-param Either<L, R> $either
 *
 * @psalm-assert-if-true Left<L, R> $either
 * @psalm-assert-if-false Right<L, R> $either
 *
 * @psalm-pure
 *
 * @phan-suppress PhanTemplateTypeNotUsedInFunctionReturn
 */
function isLeft(Either $either): bool
{
	return $either->isLeft();
}

/**
 * @psalm-template L
 * @phpstan-template L
 * @phan-template L
 *
 * @psalm-template R
 * @phpstan-template R
 * @phan-template R
 *
 * @psalm-param Either<L, R> $either
 * @phpstan-param Either<L, R> $either
 * @phan-param Either<L, R> $either
 *
 * @psalm-assert-if-true Right<L, R> $either
 * @psalm-assert-if-false Left<L, R> $either
 *
 * @psalm-pure
 *
 * @phan-suppress PhanTemplateTypeNotUsedInFunctionReturn
 */
function isRight(Either $either): bool
{
	return $either->isRight();
}

/**
 * @psalm-template L
 * @phpstan-template L
 * @phan-template L
 *
 * @psalm-template R
 * @phpstan-template R
 * @phan-template R
 *
 * @psalm-template D
 * @phpstan-template D
 * @phan-template D
 *
 * @psalm-param Either<L, R> $either
 * @phpstan-param Either<L, R> $either
 * @phan-param Either<L, R> $either
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
 * @phan-suppress PhanTemplateTypeNotUsedInFunctionReturn
 * @phan-suppress PhanTemplateTypeNotDeclaredInFunctionParams
 * @phan-suppress PhanCommentParamOutOfOrder
 */
function fromLeft(Either $either, $defaultValue)
{
	return $either->fromLeft($defaultValue);
}

/**
 * @psalm-template L
 * @phpstan-template L
 * @phan-template L
 *
 * @psalm-template R
 * @phpstan-template R
 * @phan-template R
 *
 * @psalm-template D
 * @phpstan-template D
 * @phan-template D
 *
 * @psalm-param Either<L, R> $either
 * @phpstan-param Either<L, R> $either
 * @phan-param Either<L, R> $either
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
 * @phan-suppress PhanUnusedPublicMethodParameter
 * @phan-suppress PhanTemplateTypeNotDeclaredInFunctionParams
 * @phan-suppress PhanTemplateTypeNotUsedInFunctionReturn
 * @phan-suppress PhanCommentParamOutOfOrder
 */
function fromRight(Either $either, $defaultValue)
{
	return $either->fromRight($defaultValue);
}

/**
 * @psalm-template L
 * @phpstan-template L
 * @phan-template L
 *
 * @psalm-template R
 * @phpstan-template R
 * @phan-template R
 *
 * @psalm-param iterable<int, Either<L, R>> $eithers
 * @phpstan-param iterable<int, Either<L, R>> $eithers
 * @phan-param iterable<int, Either<L, R>> $eithers
 *
 * @psalm-return Listt<int, L>
 * @phpstan-return Listt<int, L>
 * @phan-return Listt<int, L>
 *
 * @phan-suppress PhanTemplateTypeNotUsedInFunctionReturn
 * @phan-suppress PhanTypeMismatchReturn
 */
function lefts(iterable $eithers): Listt
{
	/**
	 * @phpstan-var \Closure(Either<L, R>):Maybe<L>
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
 * @psalm-template L
 * @phpstan-template L
 * @phan-template L
 *
 * @psalm-template R
 * @phpstan-template R
 * @phan-template R
 *
 * @psalm-param iterable<int, Either<L, R>> $eithers
 * @phpstan-param iterable<int, Either<L, R>> $eithers
 * @phan-param iterable<int, Either<L, R>> $eithers
 *
 * @psalm-return Listt<int, R>
 * @phpstan-return Listt<int, R>
 * @phan-return Listt<int, R>
 *
 * @phan-suppress PhanTemplateTypeNotUsedInFunctionReturn
 * @phan-suppress PhanTypeMismatchReturn
 */
function rights(iterable $eithers): Listt
{
	/**
	 * @phpstan-var \Closure(Either<L, R>):Maybe<R>
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
 * @psalm-template L
 * @phpstan-template L
 * @phan-template L
 *
 * @psalm-template R
 * @phpstan-template R
 * @phan-template R
 *
 * @psalm-param iterable<int, Either<L, R>> $eithers
 * @phpstan-param iterable<int, Either<L, R>> $eithers
 * @phan-param iterable<int, Either<L, R>> $eithers
 *
 * @psalm-return array{Listt<int, L>, Listt<int, R>}
 * @phpstan-return array{Listt<int, L>, Listt<int, R>}
 * @phan-return array{Listt<int, L>, Listt<int, R>}
 *
 * @phan-suppress PhanTemplateTypeNotUsedInFunctionReturn
 * @phan-suppress PhanTypeMismatchReturn
 */
function partitionEithers(iterable $eithers): array
{
	return [
		lefts($eithers),
		rights($eithers),
	];
}
