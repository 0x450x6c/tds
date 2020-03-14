<?php declare(strict_types=1);

namespace TDS;

/**
 * @template T
 */
interface Ord
{
	/**
	 * @param Ord $target
	 * @psalm-param T $target
	 * @phpstan-param T $target
	 * @phan-param T|mixed $target
	 */
	public function compare($target): int;
}