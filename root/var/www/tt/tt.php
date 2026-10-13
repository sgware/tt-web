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

/**
 * Reads a JSON file, parses its contents, and returns it as an object. This
 * method acquires a read lock on the file, so it is safe to use even if other
 * processes read and write to the same file. If the file does not exsit, an
 * empty JSON object is returned.
 */
function safe_read_json_file(string $file): mixed {
	$handle = @fopen($file, 'r');
	if($handle === false)
		return new stdClass();
	try {
		if(!flock($handle, LOCK_SH))
			throw new RuntimeException("The requested file could not be locked for reading.");
		$contents = stream_get_contents($handle);
		flock($handle, LOCK_UN);
	}
	finally {
		fclose($handle);
	}
	if($contents === false || trim($contents) === '')
		return new stdClass();
	else
		return json_decode($contents, JSON_THROW_ON_ERROR);
}

/**
 * Encodes a JSON object and writes it to a file. This method acquires an
 * exclusive lock on the file, so it is safe to use even if other processes
 * read and write to the same file. If the file does not exist, it is created.
 */
function safe_write_json_file(string $file, mixed $object): void {
	$contents = json_encode($object, JSON_THROW_ON_ERROR);
	$handle = fopen($file, 'c');
	if($handle === false)
		throw new RuntimeException("The requested file could not be opened.");
	try {
		if(!flock($handle, LOCK_EX))
			throw new RuntimeException("The requested file could not be locked for writing.");
		ftruncate($handle, 0);
		rewind($handle);
		if(fwrite($handle, $contents) === false)
			throw new RuntimeException("An error occurred while writing to the requested file.");
		fflush($handle);
		flock($handle, LOCK_UN);
	}
	finally {
		fclose($handle);
	}
}

/**
 * Reads the Tandem Tales session log with the given ID and returns it as a JSON
 * object. This method throws an exception if the requested session does not
 * exist.
 */
function read_session(string $id): mixed {
	check_id($id);
	$file = SESSION_LOGS . $id . '.json';
	if(file_exists($file))
		return safe_read_json_file($file);
	else
		throw new InvalidArgumentException('The session "' . $sid . '" does not exist.');
}

/**
 * Reads metadata about a Tandem Tales session log with the given ID and returns
 * it as a JSON object. If the optional $role argument is set to 'PLAYER' or
 * 'GAME_MASTER' the metadata will include information relative to that role.
 * This method throws an exception if the requested session does not exist or if
 * the $role argument is set of an invalid value.
 * 
 * The session metadata always includes:
 * 'id': The ID of the session.
 * 'world': The name, title, and description of the story world.
 * 'gm': The name, title, and description of the game master agent.
 * 'player': The name, title, and description of the player agent.
 * 'start': The time the session started in milliseconds from the epoch (1970-01-01T00:00:00Z)
 * 'end': The time the session ended in milliseconds from the epoch (1970-01-01T00:00:00Z)
 * 'duration': The duration of the session in milliseconds.
 * 'completed': True if the story reached one of its pre-defined endings.
 * 
 * If the optional $role argument is set:
 * 'role': The value of the $role argument, which must be either 'PLAYER' or 'GAME_MASTER'.
 * 'partner': The name, title, and description of the other agent.
 * 'completed': True if this role was not the reason the session ended early.
 */
function read_session_metadata(string $id, ?string $role = null): mixed {
	$session = read_session($id);
	// Session ID
	$meta['id'] = $id;
	// World
	$meta['world'] = array();
	$meta['world']['name'] = $session['world']['name'];
	$meta['world']['title'] = $session['world']['title'];
	$meta['world']['description'] = $session['world']['description'];
	// Agents
	$meta['gm'] = array();
	$meta['gm']['name'] = $session['gm']['name'];
	$meta['gm']['title'] = $session['gm']['title'];
	$meta['gm']['description'] = $session['gm']['description'];
	$meta['player'] = array();
	$meta['player']['name'] = $session['player']['name'];
	$meta['player']['title'] = $session['player']['title'];
	$meta['player']['description'] = $session['player']['description'];
	// Role and Partner
	if($role != null) {
		$role = strtoupper($role);
		if($role != 'PLAYER' and $role != 'GAME_MASTER')
			throw new InvalidArgumentException('The role \"' + $role + '" was not recognized.');
		$meta['role'] = $role;
		if($role == 'PLAYER')
			$meta['partner'] = $session['gm'];
		else
			$meta['partner'] = $session['player'];
	}
	// Start time, end time, and duration
	$start = $session['events'][0]['time'];
	$end = $session['result']['time'];
	foreach($session['events'] as $event) {
		$start = min($start, $event['time']);
		$end = max($end, $event['time']);
	}
	foreach($session['reports'] as $report) {
		$start = min($start, $report['time']);
		$end = max($end, $report['time']);
	}
	$meta['start'] = $start;
	$meta['end'] = $end;
	$meta['duration'] = $end - $start;
	// Completion
	$meta['completed'] = isset($data['result']['ending']);
	if($role != null) {
		if(isset($data['result']['role']) and $role == $data['result']['role'])
			$meta['complete'] = false;
		else
			$meta['complete'] = true;
	}
	return $meta;
}

?>