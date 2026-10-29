<?php
include($_SERVER['tt_library']);

/** The location of the server status file */
const STATUS_FILE = '/var/log/tt/status.json';

// Read the server status file.
$status = null;
$playing_html = null;
$waiting_html = null;
if(file_exists(STATUS_FILE)) {
	$status = safe_read_json_file(STATUS_FILE);
	// Playing Table
	$playing_html = "<table>\n\t<tr><th>World</th><th>Game Master</th><th>Player</th><th>Duration</th></tr>\n";
	foreach($status['playing'] as $session) {
		$playing_html .= "\t<td>";
		$playing_html .= title($session['world']);
		$playing_html .= "</td><td>";
		$playing_html .= title($session['gm']);
		$playing_html .= "</td><td>";
		$playing_html .= title($session['player']);
		$playing_html .= "</td><td>";
		$playing_html .= since($session['start']);
		$playing_html .= "</td></tr>\n";
	}
	$playing_html .= '</table>';
	// Waiting Table
	$waiting_html = "<table>\n\t<tr><th>Agent</th><th>World</th><th>Role</th><th>Partner</th><th>Wait</th></tr>\n";
	foreach($status['waiting'] as $waiting) {
		$waiting_html .= "\t<td>";
		$waiting_html .= title($waiting['agent']);
		$waiting_html .= "</td><td>";
		$waiting_html .= isset($waiting['world']) ? title($waiting['world']) : '';
		$waiting_html .= "</td><td>";
		$waiting_html .= $waiting['role'] ?? '';
		$waiting_html .= "</td><td>";
		$waiting_html .= isset($waiting['partner']) ? title($waiting['partner']) : '';
		$waiting_html .= "</td><td>";
		$waiting_html .= since($waiting['start']);
		$waiting_html .= "</td></tr>\n";
	}
	$waiting_html .= '</table>';
}

?>