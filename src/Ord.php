<?php declare(strict_types=1);

namespace TDS;

/**
 * @psalm-template T
 */
interface Ord
{
	public const EQ = 0;
	public const GT = 1;
	public const LT = -1;

	/**
	 * @param Ord $target
	 * @psalm-param T $target
	 *
	 * @psalm-return self::EQ|self::LT|self::GT
	 *
	 * @return int
	 */
	public function compare($target);
}
