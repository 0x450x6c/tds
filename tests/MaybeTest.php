<?php declare(strict_types=1);

namespace TDS\Tests;

use PHPUnit\Framework\TestCase;
use function TDS\Maybe\catMaybes;
use function TDS\Maybe\fromJust;
use function TDS\Maybe\fromMaybe;
use function TDS\Maybe\isJust;
use function TDS\Maybe\isNothing;
use TDS\Maybe\Just;
use function TDS\Maybe\just;
use function TDS\Maybe\listToMaybe;
use function TDS\Maybe\mapMaybe;
use function TDS\Maybe\maybe;
use function TDS\Maybe\maybeSelectNotNull;
use function TDS\Maybe\maybeToList;
use TDS\Maybe\Nothing;
use function TDS\Maybe\nothing;
use function TDS\Maybe\toMaybe;
use TDS\Maybe\UsingFromJustOnNothingException;

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

		$this->expectException(UsingFromJustOnNothingException::class);
		nothing()->fromJust();

		$this->expectException(UsingFromJustOnNothingException::class);
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

		$emptyList = [];

		static::assertInstanceOf(
			Nothing::class,
			listToMaybe($emptyList)
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

		static::assertSame(
			$listB,
			mapMaybe(
				$listA,
				static fn (int $item) => (
					0 === $item % 2 ? null : "test-{$item}"
				)
			)
				->toArray()
		);
	}

	public function test_using_as_iterator(): void
	{
		static::assertIsIterable(nothing());
		static::assertIsIterable(Just('a'));

		static::assertSame(
			[],
			iterator_to_array(nothing())
		);
		static::assertSame(
			['a'],
			iterator_to_array(Just('a'))
		);
	}

	public function test_using_as_countable(): void
	{
		static::assertCount(0, nothing());
		static::assertCount(1, Just('a'));
	}

	public function test_maybe_select_not_null(): void
	{
		static::assertTrue(
			maybeSelectNotNull(nothing())->isNothing()
		);
		static::assertTrue(
			maybeSelectNotNull(just(null))->isNothing()
		);

		/** @var null|string */
		$a = 'a';

		static::assertSame(
			'a',
			maybeSelectNotNull(just($a))->fromJust()
		);
	}

	public function test_serialize(): void
	{
		$justA = unserialize(serialize(just('a')));
		static::assertInstanceOf(Just::class, $justA);
		static::assertSame(
			'a',
			$justA->fromJust()
		);

		$nothing = unserialize(serialize(nothing()));
		static::assertInstanceOf(Nothing::class, $nothing);
		static::assertTrue(
			$nothing->isNothing()
		);
	}

	public function test_to_maybe(): void
	{
		static::assertSame(
			nothing(),
			toMaybe(null)
		);
		static::assertSame(
			'a',
			toMaybe('a')->fromJust()
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

	public function test_to_string(): void
	{
		static::assertSame(
			'1',
			(string) toMaybe(1)->__toString()
		);
		static::assertSame(
			'',
			(string) nothing()->__toString()
		);
	}
}
