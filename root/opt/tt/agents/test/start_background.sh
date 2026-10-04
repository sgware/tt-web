#!/bin/sh

# Create a new screen session named 'test-agent' if one does not exist.
screen -ls | grep -q "test-agent" || screen -dmS "test-agent"
# Start the Tandem Tales Test Agent in that session after a delay.
screen -S "test-agent" -p 0 -X stuff "sleep 3\n/opt/tt/agents/test/start.sh\n"
