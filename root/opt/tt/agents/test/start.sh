#!/bin/sh

# Set the agent's password.
export password="test"
# Run the Tandem Tales Test Agent
java \
  -Djavax.net.ssl.trustStore="/etc/tt/certs/tt-truststore.p12" \
  -Djavax.net.ssl.trustStorePassword="changeit" \
	-jar /opt/tt/agents/test/tt-test-agent.jar