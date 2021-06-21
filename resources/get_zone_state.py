#!/usr/bin/env python3
from pyrainbird import RainbirdController
import sys

if len(sys.argv) < 4:
  print("Usage: get_zone_state.py <ip> <password> <zone>")
  exit()

ip = sys.argv[1]
password = sys.argv[2]
zone = sys.argv[3]

controller = RainbirdController(ip, password)
getzone = controller.get_zone_state(int(zone))
print(getzone)