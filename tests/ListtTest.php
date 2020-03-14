<?php declare(strict_types=1);

namespace TDS\Tests;

use PHPUnit\Framework\TestCase;
use function TDS\Listt\concat;
use TDS\Listt\EmptyListException;
use function TDS\Listt\fromIter;
use function TDS\Listt\head;
use function TDS\Listt\headMaybe;
use TDS\Listt\IndexTooLargeException;
use function TDS\Listt\init;
use function TDS\Listt\isEmpty;
use function TDS\Listt\last;
use function TDS\Listt\lastMaybe;
use function TDS\Listt\length;
use TDS\Listt\Listt;
use function TDS\Listt\null;
use function TDS\Listt\tail;
use function TDS\Listt\uncons;
use function TDS\Maybe\just;
use function TDS\Maybe\nothing;
use TDS\Maybe\Nothing;
use TDS\Ord;

/**
 * @internal
 */
final class ListtTest extends TestCase
{
	public function test_concat(): void
	{
		self::assertList(
			['a', 'b', 'c', 'd'],
			fromIter(['a', 'b'])->concat(['c', 'd'])
		);

		self::assertList(
			['a', 'b', 'c', 'd'],
			concat(
				['a', 'b'],
				['c', 'd']
			)
		);
	}

	public function test_head(): void
	{
		$listA = ['a', 'b'];

		static::assertSame(
			'a',
			fromIter($listA)->head()
		);

		static::assertSame(
			'a',
			head($listA)
		);

		/** @var mixed[] */
		$emptyList = [];
		$this->expectException(EmptyListException::class);
		$this->expectExceptionMessage('Empty list.');
		fromIter($emptyList)->head();
	}

	public function test_head_maybe(): void
	{
		$listA = ['a', 'b'];

		static::assertSame(
			'a',
			fromIter($listA)->headMaybe()->fromJust()
		);

		static::assertSame(
			'a',
			headMaybe($listA)->fromJust()
		);

		/**
		 * @var mixed[]
		 */
		$emptyList = [];

		static::assertInstanceOf(
			Nothing::class,
			fromIter($emptyList)->headMaybe()
		);
	}

	public function test_last(): void
	{
		$listA = ['a', 'b'];

		static::assertSame(
			'b',
			fromIter($listA)->last()
		);

		static::assertSame(
			'b',
			last($listA)
		);

		/** @var mixed[] */
		$emptyList = [];
		$this->expectException(EmptyListException::class);
		$this->expectExceptionMessage('Empty list.');
		fromIter($emptyList)->last();
	}

	public function test_last_maybe(): void
	{
		$listA = ['a', 'b'];

		static::assertSame(
			'b',
			fromIter($listA)->lastMaybe()->fromJust()
		);

		static::assertSame(
			'b',
			lastMaybe($listA)->fromJust()
		);

		/**
		 * @var mixed[]
		 */
		$emptyList = [];

		static::assertInstanceOf(
			Nothing::class,
			fromIter($emptyList)->lastMaybe()
		);
	}

	public function test_tail(): void
	{
		$listA = ['a', 'b', 'c'];

		self::assertList(
			['b', 'c'],
			fromIter($listA)->tail()
		);

		self::assertList(
			[1 => 'b', 2 => 'c'],
			fromIter($listA)->tail(true)
		);

		self::assertList(
			['b', 'c'],
			tail($listA)
		);

		self::assertList(
			[1 => 'b', 2 => 'c'],
			tail($listA, true)
		);
	}

	public function test_init(): void
	{
		$listA = ['a', 'b', 'c'];

		self::assertList(
			['a', 'b'],
			fromIter($listA)->init()
		);

		self::assertList(
			['a', 'b'],
			init($listA)
		);
	}

	public function test_uncons(): void
	{
		$listA = ['a', 'b', 'c'];

		[
			$head,
			$tail
		] = fromIter($listA)->uncons()->fromJust();

		static::assertSame('a', $head);

		self::assertList(
			['b', 'c'],
			$tail
		);

		/**
		 * @var mixed[]
		 */
		$emptyList = [];
		static::assertInstanceOf(
			Nothing::class,
			fromIter($emptyList)->uncons()
		);

		[
			$head,
			$tail
		] = uncons($listA)->fromJust();

		static::assertSame('a', $head);

		self::assertList(
			['b', 'c'],
			$tail
		);

		static::assertInstanceOf(
			Nothing::class,
			uncons($emptyList)
		);
	}

	public function test_null(): void
	{
		/**
		 * @var mixed[]
		 */
		$emptyList = [];
		static::assertFalse(fromIter(['test'])->null());
		static::assertTrue(fromIter($emptyList)->null());

		static::assertFalse(fromIter(['test'])->isEmpty());
		static::assertTrue(fromIter($emptyList)->isEmpty());

		static::assertFalse(null(['test']));
		static::assertTrue(null($emptyList));

		static::assertFalse(isEmpty(['test']));
		static::assertTrue(isEmpty($emptyList));
	}

	public function test_length(): void
	{
		static::assertSame(
			3,
			length([1, 2, 3])
		);

		static::assertCount(
			3,
			fromIter([1, 2, 3])
		);

		/** @phpstan-var mixed[] */
		$emptyList = [];
		static::assertCount(
			0,
			fromIter($emptyList)
		);
	}

	public function test_to_maybe(): void
	{
		$maybe = fromIter(['a', 'b'])->toMaybe();
		static::assertSame(
			'a',
			$maybe->fromJust()
		);

		/** @phpstan-var mixed[] */
		$emptyList = [];

		static::assertInstanceOf(
			Nothing::class,
			fromIter($emptyList)->toMaybe()
		);
	}

	public function test_create_from_single_value(): void
	{
		$object = new \stdClass();

		static::assertSame([$object], Listt::from($object)->toArray());
		static::assertSame([123], Listt::from(123)->toArray());
		static::assertSame(['test'], Listt::from('test')->toArray());
		static::assertSame([null], Listt::from(null)->toArray());
		static::assertSame([true], Listt::from(true)->toArray());
		static::assertSame([false], Listt::from(false)->toArray());
	}

	public function test_create_from_iterable(): void
	{
		$list1 = [1, 2, 3];
		/** @phpstan-var Listt<int, mixed> */
		$emptyList = [];
		static::assertSame($list1, Listt::fromIter($list1)->toArray());
		static::assertSame(
			['test'],
			Listt::fromIter(Listt::from('test'))->toArray()
		);
		static::assertCount(\count($list1), Listt::fromIter($list1));
		static::assertCount(0, Listt::fromIter($emptyList));
		static::assertCount(42, Listt::fromIter($emptyList, 42));
		static::assertCount(42, Listt::fromIter($emptyList, static fn () => 42));

		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage(
			'Use `Listt::fromGenerator` for generators.'
		);
		Listt::fromIter(
			Listt::from('test')->toGenerator()
		);
	}

	public function test_create_from_generator(): void
	{
		$list1 = [1, 2, 3];
		/**
		 * @phpstan-var \Closure():\Generator<int, int>
		 */
		$generator = static fn (): \Generator => yield from $list1;

		static::assertSame(
			$list1,
			Listt::fromGenerator($generator)->toArray()
		);
		static::assertCount(
			\count($list1),
			Listt::fromGenerator($generator)
		);
		static::assertCount(
			42,
			Listt::fromGenerator($generator, 42)
		);
		static::assertCount(
			42,
			Listt::fromGenerator($generator, static fn () => 42)
		);
	}

	public function test_transform_to_generator(): void
	{
		$list1 = [1, 2, 3];
		$result = Listt::fromIter($list1)->toGenerator();
		static::assertInstanceOf(\Generator::class, $result);
		static::assertSame($list1, iterator_to_array($result));
	}

	public function test_transform_to_array(): void
	{
		$list1 = [1, 2, 3];
		static::assertSame($list1, Listt::fromIter($list1)->toArray());
	}

	public function test_map(): void
	{
		$listA = [1, 2, 3];
		$listB = ['test-2', 'test-3', 'test-4'];

		static::assertSame(
			$listB,
			Listt::fromIter($listA)
				->map(static fn (int $item) => 'test-'.($item + 1))
				->toArray()
		);
	}

	public function test_map_maybe(): void
	{
		$listA = [1, 2, 3, 4, 5];
		$listB = ['test-1', 'test-3', 'test-5'];

		static::assertSame(
			$listB,
			Listt::fromIter($listA)
				->mapMaybe(static fn (int $item) => (
					0 === $item % 2 ? nothing() : just("test-{$item}")
				))
				->toArray()
		);
	}

	public function test_select(): void
	{
		$listA = range(1, 10);
		$listB = [
			// keys are preserved
			0 => 1,
			2 => 3,
			4 => 5,
			6 => 7,
			8 => 9,
		];

		static::assertSame(
			$listB,
			Listt::fromIter($listA)
				->select(static fn (int $item): bool => 0 !== $item % 2, true)
				->toArray()
		);

		static::assertSame(
			[1, 3, 5, 7, 9],
			Listt::fromIter($listA)
				->select(static fn (int $item): bool => 0 !== $item % 2)
				->toArray()
		);
	}

	public function test_tap_and_apply(): void
	{
		$listA = [
			'a' => 'b',
			'c' => 'd',
		];
		$listB = [];

		$newListA = Listt::fromIter($listA)
			->tap(static function (string $item, string $key) use (
				&$listB
			): void {
				$listB[$key] = $item;
			})
		;

		static::assertEmpty($listB);

		$newListA->apply();

		static::assertSame($listA, $listB);
	}

	public function test_using_as_iterator(): void
	{
		$list = Listt::from('test');
		static::assertSame(
			['test'],
			iterator_to_array($list),
		);
	}

	public function test_reverse(): void
	{
		static::assertSame(
			['d', 'c', 'b', 'a'],
			fromIter(['a', 'b', 'c', 'd'])->reverse()->toArray(),
		);

		static::assertSame(
			[2 => 'c', 1 => 'b', 0 => 'a'],
			fromIter(['a', 'b', 'c'])->reverse(true)->toArray(),
		);
	}

	/**
	 * @dataProvider ntxProvider
	 * @psalm-template T of \Throwable
	 *
	 * @psalm-param Listt<int, mixed> $list
	 * @psalm-param null|class-string<T> $exception
	 * @psalm-param null|mixed $value
	 *
	 * @param mixed $value
	 */
	public function test_ntx(
		Listt $list,
		int $ntx,
		?string $exception,
		$value
	): void {
		if (null !== $value) {
			static::assertSame(
				$value,
				$list->nth($ntx)
			);

			return;
		}

		if (null !== $exception) {
			$this->expectException($exception);
			$list->nth($ntx);

			return;
		}
	}

	/**
	 * @psalm-template T of \Throwable
	 *
	 * @psalm-return \Generator<
	 *     string,
	 *     array{Listt<int, mixed>, int, class-string<T>|null, mixed|null}
	 * >
	 * @phpstan-return iterable<string, array<mixed>>
	 * @psalm-suppress all
	 */
	public function ntxProvider(): iterable
	{
		$list = fromIter(['a', 'b', 'c']);
		/** @var mixed[] */
		$emptyList = [];

		yield 'Get second element.' => [
			$list,
			1,
			null,
			'b',
		];

		yield 'Try to get large index.' => [
			$list,
			4,
			IndexTooLargeException::class,
			null,
		];

		yield 'Try to get ntx element from empty list.' => [
			fromIter($emptyList),
			0,
			IndexTooLargeException::class,
			null,
		];

		yield 'Try to get negative index.' => [
			fromIter($emptyList),
			-1,
			\InvalidArgumentException::class,
			null,
		];
	}

	public function test_minimum(): void
	{
		static::assertSame(
			'a',
			fromIter(['d', 'a', 'b', 'c'])->minimum(),
		);
		static::assertSame(
			2,
			fromIter([5, 4, 2, 3])->minimum(),
		);

		$list = [
			new _Orderable(50),
			new _Orderable(40),
			new _Orderable(20),
			new _Orderable(30),
		];

		static::assertSame(
			20,
			fromIter($list)->minimum()->n
		);
	}

	public function test_maximum(): void
	{
		static::assertSame(
			'd',
			fromIter(['d', 'a', 'b', 'c'])->maximum(),
		);
		static::assertSame(
			5,
			fromIter([5, 4, 2, 3])->maximum(),
		);

		$list = [
			new _Orderable(40),
			new _Orderable(20),
			new _Orderable(50),
			new _Orderable(30),
		];

		static::assertSame(
			50,
			fromIter($list)->maximum()->n
		);
	}

	/**
	 * @phpstan-param array<mixed> $expected
	 * @phpstan-param Listt<mixed, mixed> $actual
	 */
	private static function assertList(array $expected, Listt $actual): void
	{
		static::assertSame($expected, $actual->toArray());
	}
}

/**
 * @implements Ord<_Orderable>
 */
class _Orderable implements Ord
{
	public int $n;

	public function __construct(int $n)
	{
		$this->n = $n;
	}

	/**
	 * @param _Orderable $target
	 */
	public function compare($target): int
	{
		return $this->n - $target->n;
	}
}