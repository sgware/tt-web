#===============================================================================
# Tandem Tales Web Server Docker Image
# 
# Defines a server with everything necessary to serve webpages over HTTP and
# HTTPS, run the Tandem Tales server, and run the Tandem Tales web client.
#===============================================================================

# Start with the Ubuntu operating system.
FROM ubuntu:26.04

# Update packages.
RUN apt update
# GNU Nano is a simple text editor similar to Notepad.
RUN apt install -y nano
# GNU Screen runs commands in detachable terminals.
RUN apt install -y screen
# Apache handles HTTP and HTTPS requests.
RUN apt install -y apache2
# PHP enables server-side scripting in HTML pages.
RUN apt install -y php8.5 libapache2-mod-php
# Enable Apache to handle HTTPS.
RUN a2enmod ssl
# Git downloads the latest Tandem Tales software from GitHub.
RUN apt install -y git
# The Java JRE runs Tandem Tales.
RUN apt install -y openjdk-25-jre
# Websockify maps secure WebSockets to standard TLS sockets.
RUN apt install -y websockify
# Clean up after installing software.
RUN apt clean
RUN rm -rf /var/lib/apt/lists/*

# Copy custom shell scripts.
COPY root/usr/local/bin /usr/local/bin
COPY root/entrypoint.sh /

# Copy Tandem Tales server files and create log directories.
COPY root/opt/tt /opt/tt
RUN mkdir -p /var/log/tt/sessions
# This folder holds the public and private keys used by Apache and the Tandem
# Tales server. This volume can be mapped to a host directory if the user wants
# to provide their own keys. If not, it will be mounted as an anonymous volume
# and self-signed keys will be generated at startup. An anonymous volume
# persists when the containers restarts but not when it is rebuilt.
VOLUME /etc/tt/certs
# Download the latest Tandem Tales Server and story world files from GitHub.
RUN update_tt_server
RUN update_tt_worlds

# Copy Apache configuration files.
COPY root/etc/apache2 /etc/apache2
# Copy website content.
COPY root/var/www /var/www
# Disable the default Apache site.
RUN a2dissite 000-default.conf
# Enable the Tandem Tales website.
RUN a2ensite tt

# When this image runs non-interactively, start Apache, then start Websockify,
# in the background, then start Tandem Tales in the foreground, optionally
# updating the database with if certain environment variables are set.
CMD ["sh", "-c", "/entrypoint.sh"]