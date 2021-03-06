#!/usr/bin/env php
<?php
// vim: set ai ts=4 sw=4 ft=php:
// License for all code of this FreePBX module can be found in the license file inside the module directory
// Copyright 2013 Schmooze Com Inc.
//
// Dashboard Scheduler.
// Runs every minute.
//

// Start quickly.
$bootstrap_settings['freepbx_auth'] = false;  // Just in case.
$restrict_mods = true; // Takes startup from 0.2 seconds to 0.07 seconds.
include '/etc/freepbx.conf';

$lockfile = \FreePBX::Config()->get('ASTRUNDIR')."/scheduler.lock";

// Sleep to fix crazy issues with large VM hosting providers
sleep(mt_rand(1,30));

// Create a lock to make sure no more than one instance of this
// program can be running on a machine at a time
$fh = fopen($lockfile, "a");
if (!$fh || !flock($fh, LOCK_EX|LOCK_NB)) {
	// Unable to lock, we're already running.
	exit;
}

// Run the trigger
\FreePBX::Dashboard()->runTrigger();

// remove lockfile, and then close handle to release kernel lock
unlink($lockfile);
fclose($fh);
?>
