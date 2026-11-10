#!/usr/bin/env bash
# Starts this site's MCP server over STDIO, for an MCP client such as Claude Code:
#
#   claude mcp add mbf-local -- "/absolute/path/to/wp-content/scripts/mcp-serve.sh"
#
# Local development only. Needs WP-CLI, the MCP Adapter plugin and mu-plugins/mbf-mcp.php
# (see docs/STATUS.md). Local by Flywheel runs MySQL on a per-site socket, so this finds the
# site's ID in Local's sites.json and points PHP at that socket.
#
# STDIO carries the protocol itself, so PHP's notices go to stderr: one Deprecated line on
# stdout would break every message after it.

set -euo pipefail

content="$(cd "$(dirname "$0")/.." && pwd)"   # wp-content/
public="$(dirname "$content")"               # app/public/
site="$(dirname "$(dirname "$public")")"      # the Local site folder

id="$(SITE="$site" python3 -c '
import json, os
site = os.path.realpath(os.environ["SITE"])
sites = json.load(open(os.path.expanduser("~/Library/Application Support/Local/sites.json")))
print(next(k for k, v in sites.items() if os.path.realpath(os.path.expanduser(v["path"])) == site))
')"

socket="$HOME/Library/Application Support/Local/run/$id/mysql/mysqld.sock"

exec wp --path="$public" \
	--exec="ini_set('mysqli.default_socket','$socket'); ini_set('display_errors','stderr');" \
	mcp-adapter serve --server=mbf --user=1
