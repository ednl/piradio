#!/bin/bash

PLAYER=/usr/bin/mpc
DBFILE=/home/pi/radio.txt

curstation=
if [ -x $PLAYER ]; then
	if [[ -e "$DBFILE" && -f "$DBFILE" && -r "$DBFILE" ]]; then
		curstream=$($PLAYER -f "%file%" current)
		if [ -z "$curstream" ]; then
			curstation="off"
		else
			curstation=$(cat "$DBFILE" \
				| grep --colour=never -m 1 -F "$curstream" \
				| grep --colour=never -oP '^\S+')
			if [ -z "$curstation" ]; then
				curstation="???"
			fi
		fi
	else
		curstation="?db"
	fi
else
	curstation="?mpc"
fi

echo $curstation
exit 0
