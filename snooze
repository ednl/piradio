#!/bin/bash

ATBIN=/usr/bin/at
ATCMD='/usr/bin/mpc stop'

if [ -x $ATBIN ]; then
	for i in $(sudo $ATBIN -l | grep -oP '^\d+' | sort | xargs); do
		j=$(sudo $ATBIN -c $i | tail -n 1)
		if [[ "$j" == "$ATCMD" ]]; then
			sudo $ATBIN -l | grep -P "^$i\t" | grep -oP '\d\d:\d\d'
			exit 0
		fi
	done
else
	echo "?at"
	exit 1
fi
exit 0
