#!/usr/bin/env python3
from pyrainbird import RainbirdController
import sys

if len(sys.argv) < 5:
  print("Usage: irrigate_zone.py <ip> <password> <zone> <timer>")
  exit()

ip = sys.argv[1]
password = sys.argv[2]
zone = sys.argv[3]
timer = sys.argv[4]

controller = RainbirdController(ip, password)
controller.irrigate_zone(int(zone), int(timer))
