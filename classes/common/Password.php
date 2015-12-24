<?php
namespace common;

final class Password {
	const
		LOWER   = 0x1,
		UPPER   = 0x2,
		DIGITS  = 0x4,
		SYMBOLS = 0x8,
		ALL     = 0xF,

		ALPHA_LOWER   = 'abcdefghijklmnopqrstuwxyz',
		ALPHA_UPPER   = 'ABCDEFGHIJKLMNOPQRSTUWXYZ',
		ALPHA_DIGITS  = '0123456789',
		ALPHA_SYMBOLS = '`!@#$%^&*()_+|~-=\;:<>[]/',

		DEFAULT_LENGTH = 8
	;

	function __construct() {
		return FALSE;
	}

	static function generate(
		$length   = self::DEFAULT_LENGTH,
		$alphabetType = self::ALL
	) {

		$alphabet = '';
		if ($alphabetType & self::LOWER)   $alphabet .= self::ALPHA_LOWER;
		if ($alphabetType & self::UPPER)   $alphabet .= self::ALPHA_UPPER;
		if ($alphabetType & self::DIGITS)  $alphabet .= self::ALPHA_DIGITS;
		if ($alphabetType & self::SYMBOLS) $alphabet .= self::ALPHA_SYMBOLS;

		return substr(str_shuffle($alphabet), 0, $length);
	}

}
