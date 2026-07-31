#!/bin/sh

# Generate a self-signed public/private SSL key pair if one does not exist.
if [ ! -e /etc/tt/certs/tt-private.pem ]; then
  echo "Generating self-signed SSL keys..."
  self_sign > /dev/null 2>&1
fi

# Import the TLS keys into a Java-compatible keystore if one was not provided.
if [ ! -e /etc/tt/certs/tt-keystore.p12 ]; then
  echo "Importing SSL keys into Java-compatible keystore..."
  import_keys > /dev/null 2>&1
fi

# Remove old screen sessions.
screen -wipe > /dev/null

# Start Apache in the background.
apache2ctl start

# Start websockify in the background.
start_ws

# Start the Tandem Tales server in the foreground.
start_tt_with_db_updates