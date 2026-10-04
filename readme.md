# Tandem Tales Web Server

This repository defines a Docker image for running a web server that includes
the [Tandem Tales Server](https://github.com/sgware/tt-server), a web-based
client for human play, and other resources. It serves as both a convenient way
to run a local server for development and testing of Tandem Tales agents as well
as documentation of how all the parts of the Tandem Tales platform fit together.

It includes the following:
- The [Tandem Tales Server](http://github.com/sgware/tt-server), which runs in
  [Java](http://www.java.com).
- The [Tandem Tales Test Agent](https://github.com/sgware/tt-test-agent), so
  there is always at least one simple agent available for testing.
- The [Apache web server](http://httpd.apache.org/) with
  [PHP](http://www.php.net/) for server-side scripting.
- [OpenSSL](http://www.openssl.org/) for generating a self-signed public and
  private key used for encryption.
- [websockify](http://github.com/novnc/websockify), to allow JavaScript
  WebSockets to connect to Tandem Tales from a web browser, which runs in
	[Python](https://www.python.org/).
- The [Tandem Tales Web Player](https://github.com/sgware/tt-web-player), which
  lets you play Tandem Tales via a web browser.
- Some tools for compiling web pages from simple markdown source, including
  [Pandoc](https://pandoc.org/) and [Tidy](https://www.html-tidy.org/).

Because this server requires several different technologies to be properly
configured, it is published as a [Docker](http://docker.com) image, which is
basically a lightweight virtual machine that should make it easy to set up and
run this server.

## Download, Build, and Run

Make sure [Docker Compose](http://docker.com) is running. Make sure
[Git](http://git-scm.com) and Docker are on your path. Then open a terminal and
type these commands:
```
git clone https://github.com/sgware/tt-web.git
cd tt-web
docker compose build
docker compose up
```
Then open a web browser and navigate to [https://localhost](https://localhost).
Because the server is using a self-signed encryption certificate, you will
probably see a warning about it being insecure. Accept the warning to see a
simple Tandem Tales website.

You can connect to the Tandem Tales server on port 9005 using any TLS client or
on port 9006 using a secure WebSocket.