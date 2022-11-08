#!/usr/bin/env python3
from pyrainbird import RainbirdController
import sys

if len(sys.argv) < 4:
  print("Usage: advance_zone.py <ip> <password> <numbzone>")
  exit()

ip = sys.argv[1]
password = sys.argv[2]
numbzone = sys.argv[3]

controller = RainbirdController(ip, password)
controller.advance_zone(numbzone)