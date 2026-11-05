#!/bin/sh

# If a screen session named 'tta-test' exists, stop it.
if screen -ls | grep -q "tta-test"; then
	echo "Stopping Tandem Tales Test agent..."
	screen -S tta-test -X stuff $'\003'
fi