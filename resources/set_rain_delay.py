#!/usr/bin/env python3
from pyrainbird import RainbirdController
import sys

if len(sys.argv) < 4:
  print("Usage: set_rain_delay.py <ip> <password> <days>")
  exit()

ip = sys.argv[1]
password = sys.argv[2]
days = sys.argv[3]

controller = RainbirdController(ip, password)
controller.set_rain_delay(days)
