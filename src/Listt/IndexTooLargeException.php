<?php declare(strict_types=1);

namespace TDS\Listt;

final class IndexTooLargeException extends \Exception
{
	public function __construct(int $index, int $length)
	{
		parent::__construct(
			sprintf(
				'Index "%d" too large: length "%d".',
				$index,
				$length
			)
		);
	}
}
