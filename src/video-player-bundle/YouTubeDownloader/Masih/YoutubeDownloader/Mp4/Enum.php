<?php

/**
 * Enum
 *
 * @author Masih Yeganeh <masihyeganeh@outlook.com>
 * @package YoutubeDownloader
 *
 * @version 2.9.5
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace VideoPlayerBundle\YouTubeDownloader\Masih\YoutubeDownloader\Mp4;

abstract class Enum
{
	public static function of($value)
	{
		$c = new \ReflectionClass(static::class);
		$key = array_search($value, $c->getConstants());
		if($key === false) {
			throw new \InvalidArgumentException();
		}
		return $key;
	}
}