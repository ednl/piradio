piradio
=======

This project uses the GNU General Public License version 3, see the included LICENSE file.

Raspberry Pi internet radio. Radio station playback using the mpd media server, controlled via the command line, a web interface or Pimoroni "Display-o-Tron 3000" HAT. Main work is in the "piradio" bash script.

Control with DotHAT using this Python script: https://github.com/ednl/python/blob/master/radiocontrols.py
- Add "station" and "snooze" scripts to /usr/local/bin
- Add "radiocontrols.py" script to ~/bin or /usr/local/bin
- Edit /etc/rc.local, add line before exit: "/home/pi/bin/radiocontrols.py &"
