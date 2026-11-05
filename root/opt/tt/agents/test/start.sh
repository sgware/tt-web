#!/bin/sh

echo "Starting Tandem Tales Test agent in a separate screen..."

# Create a new screen session named 'tta-test' if one does not exist.
screen -ls | grep -q "tta-test" || screen -dmS "tta-test"

# Start the Tandem Tales Test agent in that screen session.
screen -S "tta-test" -p 0 -X stuff "/opt/tt/agents/test/start_foreground.sh\n"