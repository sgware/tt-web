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

# Create a self-signed public/private encryption key pair.
RUN self-sign

# Copy Tandem Tales server files and create log directories.
COPY root/opt/tt /opt/tt
RUN mkdir -p /var/log/tt/sessions
# Download the latest Tandem Tales Server and story world files from GitHub.
RUN update_tt_server
RUN update_tt_worlds
# Import the server's public and private keys into a keystore Java can use.
RUN import_keys

# Copy Apache configuration files.
COPY root/etc/apache2 /etc/apache2
# Copy website content.
COPY root/var/www /var/www
# Disable the default Apache site.
RUN a2dissite 000-default.conf
# Enable the Tandem Tales website.
RUN a2ensite tt

# When this image runs non-interactively, start Apache, then start Websockify,
# then start Tandem Tales in the foreground so the container does not exit.
CMD ["sh", "-c", "apache2ctl start && start_ws && start_tt_foreground"]