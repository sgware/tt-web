<?php

/** The directory where Tandem Tales session logs are stored. */
const SESSION_LOGS = '/var/log/tt/sessions/';

/** The characters allowed to appear in an ID string. */
const ID_CHARS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

/** The minimum allowed length of an ID string. */
const ID_MIN_LENGTH = 5;

/** The maximum allowed length of an ID string. */
const ID_MAX_LENGTH = 25;

/**
 * Generates a random alphanumeric ID string of a given length.
 */
function generate_id($length=20) {
	$string = '';
	for ($i = 0; $i < $length; $i++)
		$string .= ID_CHARS[random_int(0, strlen(ID_CHARS) - 1)];
	return $string;
}

/**
 * Checks whether a given string is a valid alphanumeric ID string. This check
 * is important because IDs are sometimes used as file names, so ID strings
 * need to be valid file names.
 */
function check_id($string) {
	if(strlen($string) < ID_MIN_LENGTH)
		throw new InvalidArgumentException("The ID string \"" . $string . "\" is too short.");
	else if(strlen($string) > ID_MAX_LENGTH)
		throw new InvalidArgumentException("The ID string \"" . $string . "\" is too long.");
	for ($i = 0; $i < strlen($string); $i++)
		if(!str_contains(ID_CHARS, $string[$i]))
			throw new InvalidArgumentException("The ID string \"" . $string . "\" contains illegal characters.");
}

?>