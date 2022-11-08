#!/usr/bin/env python3
from pyrainbird import RainbirdController
import sys

if len(sys.argv) < 3:
  print("Usage: get_rain_sensor_state.py <ip> <password>")
  exit()

ip = sys.argv[1]
password = sys.argv[2]

controller = RainbirdController(ip, password)
getrainsensor = controller.get_rain_sensor_state()
print(getrainsensor)