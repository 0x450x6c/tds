<?php declare(strict_types=1);

namespace TDS\Tests;

use PHPUnit\Framework\TestCase;
use function TDS\Maybe\catMaybes;
use function TDS\Maybe\fromJust;
use TDS\Maybe\FromJustNothingException;
use function TDS\Maybe\fromMaybe;
use function TDS\Maybe\isJust;
use function TDS\Maybe\isNothing;
use function TDS\Maybe\just;
use function TDS\Maybe\listToMaybe;
use function TDS\Maybe\mapMaybe;
use function TDS\Maybe\maybe;
use function TDS\Maybe\maybeToList;
use TDS\Maybe\Nothing;
use function TDS\Maybe\nothing;

/**
 * @internal
 */
final class MaybeTest extends TestCase
{
	public function test_maybe(): void
	{
		static::assertSame(
			'a',
			nothing()->maybe('a', static fn (): string => 'b')
		);

		static::assertSame(
			'b',
			just('b')
				->maybe('a', static fn (string $x): string => $x)
		);

		static::assertSame(
			'a',
			maybe(nothing(), 'a', static fn (): string => 'b')
		);

		static::assertSame(
			'b',
			maybe(
				just('b'),
				'a',
				static fn (string $x): string => $x
			)
		);
	}

	public function test_is_just(): void
	{
		$justA = just('a');
		static::assertTrue(
			$justA->isJust()
		);

		static::assertTrue(
			isJust($justA)
		);

		static::assertFalse(
			nothing()->isJust()
		);

		static::assertFalse(
			isJust(nothing())
		);
	}

	public function test_is_nothing(): void
	{
		$justA = just('a');
		static::assertFalse(
			$justA->isNothing()
		);

		static::assertFalse(
			isNothing($justA)
		);

		static::assertTrue(
			nothing()->isNothing()
		);

		static::assertTrue(
			isNothing(nothing())
		);
	}

	public function test_from_just(): void
	{
		$justA = just('a');
		static::assertSame(
			'a',
			$justA->fromJust()
		);

		static::assertSame(
			'a',
			fromJust($justA)
		);

		$this->expectException(FromJustNothingException::class);
		nothing()->fromJust();

		$this->expectException(FromJustNothingException::class);
		fromJust(nothing());
	}

	public function test_from_maybe(): void
	{
		$justA = just('a');
		static::assertSame(
			'a',
			$justA->fromMaybe('b')
		);

		static::assertSame(
			'a',
			fromMaybe($justA, 'b')
		);

		static::assertSame(
			'b',
			nothing()->fromMaybe('b')
		);

		static::assertSame(
			'b',
			fromMaybe(nothing(), 'b')
		);
	}

	public function test_list_to_maybe(): void
	{
		static::assertSame(
			'a',
			listToMaybe(['a', 'b'])->fromJust()
		);

		/** @phpstan-var mixed[] */
		$emptyList = [];

		static::assertInstanceOf(
			Nothing::class,
			listToMaybe($emptyList)
		);
	}

	public function test_maybe_to_list(): void
	{
		static::assertSame(
			['a'],
			just('a')->toList()->toArray()
		);

		static::assertSame(
			['a'],
			maybeToList(just('a'))->toArray()
		);

		static::assertSame(
			[],
			nothing()->toList()->toArray()
		);

		static::assertSame(
			[],
			maybeToList(nothing())->toArray()
		);
	}

	public function test_cat_maybes(): void
	{
		static::assertSame(
			['a', 'b', 'c'],
			catMaybes([
				nothing(),
				just('a'),
				nothing(),
				nothing(),
				just('b'),
				just('c'),
			])
				->toArray()
		);
	}

	public function test_map_maybe(): void
	{
		$listA = [1, 2, 3, 4, 5];
		$listB = ['test-1', 'test-3', 'test-5'];

		static::assertSame(
			$listB,
			mapMaybe(
				$listA,
				static fn (int $item) => (
					0 === $item % 2 ? nothing() : just("test-{$item}")
				)
			)
				->toArray()
		);
	}
}