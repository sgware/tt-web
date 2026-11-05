#!/bin/sh

echo "Installing the Tandem Tales Test agent from GitHub..."
git clone https://github.com/sgware/tt-test-agent.git /tmp/tt-test-agent
cp -rf /tmp/tt-test-agent/build/jar/tt-test-agent.jar /opt/tt/agents/test/tt-test-agent.jar
rm -rf /tmp/tt-test-agent