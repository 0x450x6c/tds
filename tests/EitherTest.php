<?php declare(strict_types=1);

namespace TDS\Tests;

use PHPUnit\Framework\TestCase;
use function TDS\Either\either;
use function TDS\Either\fromLeft;
use function TDS\Either\fromRight;
use function TDS\Either\isLeft;
use function TDS\Either\isRight;
use TDS\Either\Left;
use function TDS\Either\left;
use function TDS\Either\lefts;
use function TDS\Either\partitionEithers;
use TDS\Either\Right;
use function TDS\Either\right;
use function TDS\Either\rights;

/**
 * @internal
 */
final class EitherTest extends TestCase
{
	public function test_either(): void
	{
		static::assertSame(
			'left',
			either(
				left('left'),
				static fn (string $l): string => $l,
				static fn (): string => 'right'
			)
		);

		static::assertSame(
			'right',
			either(
				right('right'),
				static fn (): string => 'left',
				static fn (string $r): string => $r
			)
		);

		static::assertSame(
			'left',
			left('left')->either(
				static fn (string $l): string => $l,
				static fn (): string => 'right'
			)
		);

		static::assertSame(
			'right',
			right('right')->either(
				static fn (): string => 'left',
				static fn (string $r): string => $r
			)
		);
	}

	public function test_is_left_and_is_right(): void
	{
		static::assertTrue(
			isLeft(left('a'))
		);
		static::assertFalse(
			isLeft(right('a'))
		);
		static::assertTrue(
			left('a')->isLeft()
		);
		static::assertFalse(
			left('a')->isRight()
		);
		static::assertTrue(
			isRight(right('a'))
		);
		static::assertFalse(
			isRight(left('a'))
		);
		static::assertTrue(
			right('a')->isRight()
		);
		static::assertFalse(
			right('a')->isLeft()
		);
	}

	public function test_from_left(): void
	{
		static::assertSame(
			'left',
			fromLeft(
				left('left'),
				'test'
			)
		);

		static::assertSame(
			'test',
			fromLeft(
				right('right'),
				'test'
			)
		);

		static::assertSame(
			'left',
			left('left')->fromLeft(
				'test'
			)
		);

		static::assertSame(
			'test',
			right('right')->fromLeft(
				'test'
			)
		);
	}

	public function test_from_right(): void
	{
		static::assertSame(
			'right',
			fromRight(
				right('right'),
				'test'
			)
		);

		static::assertSame(
			'test',
			fromRight(
				left('left'),
				'test'
			)
		);

		static::assertSame(
			'right',
			right('right')->fromRight(
				'test'
			)
		);

		static::assertSame(
			'test',
			left('left')->fromRight(
				'test'
			)
		);
	}

	public function test_lefts(): void
	{
		static::assertSame(
			['left-a', 'left-b', 'left-c'],
			lefts(
				[
					right('right-a'),
					left('left-a'),
					right('right-b'),
					left('left-b'),
					right('right-c'),
					left('left-c'),
				]
			)
				->toArray()
		);
	}

	public function test_rights(): void
	{
		static::assertSame(
			['right-a', 'right-b', 'right-c'],
			rights(
				[
					right('right-a'),
					left('left-a'),
					right('right-b'),
					left('left-b'),
					right('right-c'),
					left('left-c'),
				]
			)
				->toArray()
		);
	}

	public function test_partition_eithers(): void
	{
		$listA = ['left-a', 'left-b', 'left-c'];
		$listB = ['right-a', 'right-b', 'right-c'];

		$result = partitionEithers(
			[
				right('right-a'),
				left('left-a'),
				right('right-b'),
				left('left-b'),
				right('right-c'),
				left('left-c'),
			]
		);

		static::assertCount(2, $result);
		static::assertSame($listA, $result[0]->toArray());
		static::assertSame($listB, $result[1]->toArray());
	}

	public function test_serialize_left(): void
	{
		$left = unserialize(serialize(left('test')));

		static::assertInstanceOf(Left::class, $left);

		static::assertSame(
			'test',
			$left->maybeLeft()->fromJust()
		);
	}

	public function test_serialize_right(): void
	{
		$right = unserialize(serialize(right('test')));

		static::assertInstanceOf(Right::class, $right);

		static::assertSame(
			'test',
			$right->maybeRight()->fromJust()
		);
	}
}
