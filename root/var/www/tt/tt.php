<?php

/** The directory where Tandem Tales session logs are stored. */
const SESSION_LOGS = '/var/log/tt/sessions/';

/** The directory where study logs are stored. */
const STUDY_LOGS = '/var/log/tt/studies/';

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
		return array();
	else
		return json_decode($contents, true, JSON_THROW_ON_ERROR);
}

/**
 * Encodes a JSON object and writes it to a file. This method acquires an
 * exclusive lock on the file, so it is safe to use even if other processes
 * read and write to the same file. If the file does not exist, it is created.
 */
function safe_write_json_file(string $file, mixed $object): void {
	$contents = json_encode($object, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
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
 * 'version': The version number of the Tandem Tales server that created the log file.
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
	// Tandem Tales Version
	$mega['version'] = $session['version'];
	// World
	$meta['world'] = array();
	$meta['world']['name'] = $session['world']['name'];
	$meta['world']['title'] = $session['world']['title'] ?? $session['world']['name'];
	$meta['world']['description'] = $session['world']['description'] ?? '';
	// Agents
	$meta['gm'] = array();
	$meta['gm']['name'] = $session['gm']['name'];
	$meta['gm']['title'] = $session['gm']['title'] ?? $session['gm']['name'];
	$meta['gm']['description'] = $session['gm']['description'] ?? '';
	$meta['player'] = array();
	$meta['player']['name'] = $session['player']['name'];
	$meta['player']['title'] = $session['player']['title'] ?? $session['player']['name'];
	$meta['player']['description'] = $session['player']['description'] ?? '';
	// Role and Partner
	if($role != null) {
		$role = strtoupper($role);
		if($role != 'PLAYER' and $role != 'GAME_MASTER')
			throw new InvalidArgumentException('The role \"' + $role + '" was not recognized.');
		$meta['role'] = $role;
		if($role == 'PLAYER')
			$meta['partner'] = $meta['gm'];
		else
			$meta['partner'] = $meta['player'];
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
	if(array_key_exists('ending', $session['result'])) {
		$meta['complete'] = true;
		$meta['result'] = $session['result']['ending']['description'];
	}
	else {
		$meta['complete'] = false;
		$meta['result'] = $session['result']['message'];
	}
	if($role != null) {
		if(array_key_exists('role', $session['result']) and $role == $session['result']['role'])
			$meta['complete'] = false;
		else
			$meta['complete'] = true;
	}
	return $meta;
}

/**
 * Converts an array of session summary meta-data objects into an HTML table
 * that summarizes the important information about each session.
 */
function summarize_sessions($sessions) {
	$table = '<table><tr><th>World</th><th>Role</th><th>Partner</th><th>Ending</th><th>Time</th></tr>';
	foreach($sessions as $session) {
		$table .= '<tr><td>';
		$table .= $session['world']['title'];
		$table .= '</td><td>';
		if(array_key_exists('role', $session))
			$table .= str_replace('_', ' ', strtolower($session['role']));
		$table .= '</td><td>';
		if(array_key_exists('partner', $session))
			$table .= $session['partner']['description'];
		$table .= '</td><td>';
		$table .= $session['result'];
		$table .= '</td><td>';
		if($session['duration'] < 60000)
			$table .= round($session['duration'] / 1000, 0) . ' seconds';
		else
			$table .= round($session['duration'] / 60000, 1) . ' minutes';
		$table .= '</td></tr>';
	}
	$table .= '</table>';
	return $table;
}

/**
 * A dynamic object can be created from a JSON file, has magic getters and
 * setters for any value, and can be saved back to the file from which it was
 * read.
 */
abstract class Dynamic implements \JsonSerializable {
	
	/** The JSON file from which this object was read */
	private string $file;
	
	/** The key/value pairs stored in this object */
	private array $data;
	
	/**
	 * Creates a dynamic objects from a file URL. If the provided URL does not
	 * exist, the object will have no key/value pairs.
	 */
	public function __construct(string $file) {
		$this->file = $file;
		if(file_exists($file))
			$this->data = safe_read_json_file($file, JSON_THROW_ON_ERROR);
		else
			$this->data = array();
	}
	
	/**
	 * Writes the object's key/value pairs back to the JSON file from which it
	 * was read.
	 */
	public function save(): void {
		safe_write_json_file($this->file, $this);
	}
	
	/**
	 * Returns the key/value pairs which should be serialized for this object.
	 */
	public function jsonSerialize(): array {
		return $this->data;
	}
	
	/**
	 * Returns true if this object has a given key and that key's value is not
	 * null.
	 */
	public function __isset($name): bool {
		if(!array_key_exists($name, $this->data))
			return false;
		else
			return $this->data[$name] != null;
	}
	
	/**
	 * Returns the value that corresponds to a given key. This magic getter
	 * returns its result by value, allowing objects such as arrays to be easily
	 * modified.
	 */
	public function &__get(string $name): mixed {
		if(array_key_exists($name, $this->data))
			return $this->data[$name];
		else {
			$value = null;
			return $value;
		}
	}
	
	/**
	 * This magic setter creaes a new key/value pair or sets an existing key to
	 * the given value.
	 */
	public function __set(string $name, mixed $value): void {
		$this->data[$name] = $value;
	}
}

/**
 * Each Tandem Tales study stores a global object in its directory to store
 * study-wide information, such as the number of members in the story's groups.
 */
class Study extends Dynamic {
	
	/**
	 * Reads a global study object from file or creates a new one. At a minimum,
	 * a study object has the key 'id' which is the unique system name of its
	 * study, typically a single word to identify the study.
	 */
	public function __construct(string $file) {
		if(file_exists($file))
			parent::__construct($file);
		else {
			parent::__construct($file);
			$this->id = pathinfo($file, PATHINFO_FILENAME);
		}
	}
	
	/**
	 * Finds or creates the global study objects for a given study ID. This method
	 * throws an exception if the study log directory does not contain a directory
	 * with the given name.
	 */
	public static function load(string $id): self {
		check_id($id);
		$file = STUDY_LOGS . $id;
		if(!is_dir($file))
			throw new InvalidArgumentException('The requested study does not exist.');
		$file .= '/' . $id . '.json';
		return new Study($file);
	}
	
	/**
	 * Finds or creates a user record object for a user of this study. If no user
	 * ID is given, this method attempts to read it from the page's GET
	 * parameters, and failing that generates a new user ID.
	 */
	public function user(?string $id = null): User {
		if($id === null)
			$id = $_GET['user'] ?? generate_id();
		check_id($id);
		$file = STUDY_LOGS . $this->id . '/' . $id . '.json';
		return new User($file);
	}
	
	public function distribute($user, string ...$groups): string {
		if(count($groups) < 1)
			throw new InvalidArgumentException('At least one group must be provided.');
		# Create the stury group array if it does not exist.
		if($this->groups == null)
			$this->groups = array();
		# Find the name and size of the smallest group.
		$smallest = null;
		$fewest = 0;
		foreach($groups as $group) {
			# Create the group if it does not exist.
			if(!array_key_exists($group, $this->groups))
				$this->groups[$group] = array();
			# Check if the user is already in this group.
			if(in_array($user->id, $this->groups[$group])) {
				$smallest = $group;
				$fewest = -1;
			}
			# Check if this group is the smallest so far.
			$size = count($this->groups[$group]);
			if($smallest == null or $size < $fewest) {
				$smallest = $group;
				$fewest = $size;
			}
		}
		# Add the user to the smallest group.
		if(!in_array($user->id, $this->groups[$smallest])) {
			$this->groups[$smallest][] = $user->id;
			$user->group = $smallest;
		}
		return $smallest;
	}
}

/**
 * Each user in a Tandem Tales study stores record of its sessions and any data
 * collected by the study.
 */
class User extends Dynamic {
	
	/**
	 * Reads a study user's record object from file or creates a new one. At a
	 * minimum, a user object has two keys:
	 * 'id': A system name for this user unique to this study (but not necessarily
	 *   globally unique), which is typically a random alpha-numberic string.
	 * 'sessions': An array of the sessions this user that completed as part of
	 *   its study.
	 */
	public function __construct(string $file) {
		if(file_exists($file))
			parent::__construct($file);
		else {
			parent::__construct($file);
			$this->id = pathinfo($file, PATHINFO_FILENAME);
			$this->sessions = array();
		}
	}
	
	/**
	 * Returns a (possibly empty) list of sessions this user has participated in
	 * that meet the given criteria.
	 */
	public function getSessions(?string $world = null, ?string $role = null, ?string $partner = null, bool $complete = false): array {
		$list = array();
		foreach($this->sessions as $id => $session) {
			if($world != null and strcasecmp($world, $session['world']['name']) != 0)
				continue;
			if($role != null and (!array_key_exists('role', $session) or strcasecmp($role, $session['role']) != 0))
				continue;
			if($partner != null and (!array_key_exists('partner', $session) or strcasecmp($partner, $session['partner']['name']) != 0))
				continue;
			if($complete and !$session['complete'])
				continue;
			$list[$id] = $session;
		}
		return $list;
	}
	
	/**
	 * Returns the most recent session this user has participated in that meets
	 * the given criteria, or null if no session meets the criteria.
	 */
	public function getSession(?string $world = null, ?string $role = null, ?string $partner = null, bool $complete = false): ?array {
		$list = $this->getSessions($world, $role, $partner, $complete);
		$latest = null;
		foreach($list as $id => $session)
			if($latest == null or $session['end'] > $latest['end'])
				$latest = $session;
		return $latest;
	}
	
	/**
	 * Returns the total number of milliseconds that this user spent playing the
	 * sessions that meet the given criteria.
	 */
	public function getDuration(?string $world = null, ?string $role = null, ?string $partner = null, bool $complete = false): int {
		$list = $this->getSessions($world, $role, $partner, $complete);
		$total = 0;
		foreach($list as $id => $session)
			$total += $session['duration'];
		return $total;
	}
}

?>