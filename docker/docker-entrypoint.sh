#!/bin/bash
set -euo pipefail

# usage: file_env VAR [DEFAULT]
#    ie: file_env 'XYZ_DB_PASSWORD' 'example'
# (will allow for "$XYZ_DB_PASSWORD_FILE" to fill in the value of
#  "$XYZ_DB_PASSWORD" from a file, especially for Docker's secrets feature)
file_env() {
	local var="$1"
	local fileVar="${var}_FILE"
	local def="${2:-}"
	if [ "${!var:-}" ] && [ "${!fileVar:-}" ]; then
		echo >&2 "error: both $var and $fileVar are set (but are exclusive)"
		exit 1
	fi
	local val="$def"
	if [ "${!var:-}" ]; then
		val="${!var}"
	elif [ "${!fileVar:-}" ]; then
		val="$(< "${!fileVar}")"
	fi
	export "$var"="$val"
	unset "$fileVar"
}

if [ "$1" == php-fpm ]; then
	if [ "$(id -u)" = '0' ]; then
		user='www-data'
		group='www-data'
	else
		user="$(id -u)"
		group="$(id -g)"
	fi


	# allow any of these "Authentication Unique Keys and Salts." to be specified via
	# environment variables with a "LUMEN_" prefix (ie, "LUMEN_AUTH_KEY")
	uniqueEnvs=(
		APP_KEY
	)
	envs=(
		LUMEN_APP_NAME
		LUMEN_APP_ENV
		LUMEN_APP_DEBUG
		LUMEN_APP_URL
		LUMEN_DB_CONNECTION
		LUMEN_DB_HOST
		LUMEN_DB_PORT
		LUMEN_DB_DATABASE
		LUMEN_DB_USERNAME
		LUMEN_DB_PASSWORD
		LUMEN_REDIS_HOST
		"${uniqueEnvs[@]/#/LUMEN_}"
		LUMEN_CONFIG_EXTRA
	)
	haveConfig=
	for e in "${envs[@]}"; do
		file_env "$e"
		if [ -z "$haveConfig" ] && [ -n "${!e}" ]; then
			haveConfig=1
		fi
	done


	# only touch ".env" if we have environment-supplied configuration values
	if [ "$haveConfig" ]; then
		: "${LUMEN_DB_HOST:=mysql}"
		: "${LUMEN_DB_USERNAME:=root}"
		: "${LUMEN_DB_PASSWORD:=}"
		: "${LUMEN_DB_DATABASE:=LUMEN}"

		# version 4.4.1 decided to switch to windows line endings, that breaks our seds and awks
		# https://github.com/docker-library/wordpress/issues/116
		# https://github.com/WordPress/WordPress/commit/1acedc542fba2482bab88ec70d4bea4b997a92e4
		if [ -n "$(ls -A .env*)" ]; then
			sed -ri -e 's/\r$//' .env*
		else
			echo >&2 "Error: both .env and .env.example not found in $PWD (but are needed)"
			exit 1
		fi
		
		if [ ! -e .env ]; then
			echo >&2 "LUMEN not found in $PWD - copying now..."
			
			# cp .env.example .env
			awk '
				/^\/\*.*stop editing.*\*\/$/ && c == 0 {
					c = 1
					system("cat")
					if (ENVIRON["LUMEN_CONFIG_EXTRA"]) {
						print "// LUMEN_CONFIG_EXTRA"
						print ENVIRON["LUMEN_CONFIG_EXTRA"] "\n"
					}
				}
				{ print }
			' .env.example > .env 
			
			chown "$user:$group" .env
		elif [ -e .env ] && [ -n "$LUMEN_CONFIG_EXTRA" ] && [[ "$(< .env)" != *"$LUMEN_CONFIG_EXTRA"* ]]; then
			# (if the config file already contains the requested PHP code, don't print a warning)
			echo >&2
			echo >&2 'WARNING: environment variable "LUMEN_CONFIG_EXTRA" is set, but ".env" already exists'
			echo >&2 '  The contents of this variable will _not_ be inserted into the existing ".env" file.'
			echo >&2 '  (see https://github.com/docker-library/wordpress/issues/333 for more details)'
			echo >&2
		fi

		# see http://stackoverflow.com/a/2705678/433558
		sed_escape_lhs() {
			echo "$@" | sed -e 's/[]\/$*.^|[]/\\&/g'
		}
		sed_escape_rhs() {
			echo "$@" | sed -e 's/[\/&]/\\&/g'
		}
		php_escape() {
			local escaped="$(php -r 'var_export(('"$2"') $argv[1]);' -- "$1")"
			if [ "$2" = 'string' ] && [ "${escaped:0:1}" = "'" ]; then
				escaped="${escaped//$'\n'/"' + \"\\n\" + '"}"
			fi
			echo "$escaped"
		}
		set_config() {
			key="$1"
			value="$2"
			var_type="${3:-string}"
			
			#echo "s/($(sed_escape_lhs "$key"))\s*(=)\s*(.*)$/\1\2$(sed_escape_rhs "$value")/"
			sed -ri -e "s/($(sed_escape_lhs "$key"))\s*(=)\s*(.*)$/\1\2$(sed_escape_rhs "$value")/" .env
		}
		
		set_config 'DB_CONNECTION' 'mysql'
		set_config 'APP_NAME' "$LUMEN_APP_NAME"
		set_config 'DB_HOST' "$LUMEN_DB_HOST"
		set_config 'DB_USERNAME' "$LUMEN_DB_USERNAME"
		set_config 'DB_PASSWORD' "$LUMEN_DB_PASSWORD"
		set_config 'DB_DATABASE' "$LUMEN_DB_DATABASE"
		set_config 'REDIS_HOST' "$LUMEN_REDIS_HOST"
		

		for unique in "${uniqueEnvs[@]}"; do
			uniqVar="LUMEN_$unique"
			if [ -n "${!uniqVar}" ]; then
				set_config "$unique" "${!uniqVar}"
			else
				# if not specified, let's generate a random value
				currentVal="$(sed -rn -e "s/^$unique=(.*)/\1/p" .env)"
				if [ "$currentVal" = '' ]; then
					php artisan key:generate
					#set_config "$unique" "base64:$(head -c1m /dev/urandom | sha1sum | cut -d' ' -f1 | base64)"
				fi
			fi
		done

		if [ "$LUMEN_APP_DEBUG" ]; then
			set_config 'APP_DEBUG' 1 boolean
		fi

		if ! TERM=dumb php -- <<'EOPHP'
<?php
// database might not exist, so let's try creating it (just to be safe)
$stderr = fopen('php://stderr', 'w');
// https://codex.wordpress.org/Editing_.env#MySQL_Alternate_Port
//   "hostname:port"
// https://codex.wordpress.org/Editing_.env#MySQL_Sockets_or_Pipes
//   "hostname:unix-socket-path"
list($host, $socket) = explode(':', getenv('LUMEN_DB_HOST'), 2);
$port = 0;
if (is_numeric($socket)) {
	$port = (int) $socket;
	$socket = null;
}
$user = getenv('LUMEN_DB_USERNAME');
$pass = getenv('LUMEN_DB_PASSWORD');
$dbName = getenv('LUMEN_DB_DATABASE');

$maxTries = 10;
do {
	$mysql = new mysqli($host, $user, $pass, '', $port, $socket);
	if ($mysql->connect_error) {
		fwrite($stderr, "\n" . 'MySQL Connection Error: (' . $mysql->connect_errno . ') ' . $mysql->connect_error . "\n");
		--$maxTries;
		if ($maxTries <= 0) {
			exit(1);
		}
		sleep(3);
	}
} while ($mysql->connect_error);
if (!$mysql->query('CREATE DATABASE IF NOT EXISTS `' . $mysql->real_escape_string($dbName) . '`')) {
	fwrite($stderr, "\n" . 'MySQL "CREATE DATABASE" Error: ' . $mysql->error . "\n");
	$mysql->close();
	exit(1);
}
$mysql->close();
EOPHP
		then
			echo >&2
			echo >&2 "WARNING: unable to establish a database connection to '$LUMEN_DB_HOST'"
			echo >&2 '  continuing anyways (which might have unexpected results)'
			echo >&2
		fi
	fi
	
	
	echo "running composer update"
	composer update 
	
	# Limpiar la BD
	echo "running php artisan migrate:refresh --seed"
	php artisan migrate:refresh --seed
	
	
	# now that we're definitely done writing configuration, let's clear out the relevant envrionment variables (so that stray "phpinfo()" calls don't leak secrets from our code)
	for e in "${envs[@]}"; do
		unset "$e"
	done
fi

exec "$@"