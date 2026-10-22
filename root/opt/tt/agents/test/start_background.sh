#!/bin/sh

# Create a new screen session named 'test-agent' if one does not exist.
screen -ls | grep -q "test-agent" || screen -dmS "test-agent"
# Wait for Tandem Tales to start.

# Start the Tandem Tales Test Agent in the screen session.
screen -S "test-agent" -p 0 -X stuff "/opt/tt/agents/test/start.sh\n"
