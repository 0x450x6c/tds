<?php declare(strict_types=1);

namespace TDS;

/**
 * @psalm-template T
 * @phpstan-template T
 * @phan-template T
 */
interface Ord
{
	public const EQ = 0;
	public const GT = 1;
	public const LT = -1;

	/**
	 * @param Ord $target
	 * @psalm-param T $target
	 * @phpstan-param T $target
	 * @phan-param T|mixed $target
	 *
	 * @psalm-return self::EQ|self::LT|self::GT
	 * @phpstan-return int(1)|int(0)|int(-1)
	 * @phan-return int
	 *
	 * @return int
	 */
	public function compare($target);
}
