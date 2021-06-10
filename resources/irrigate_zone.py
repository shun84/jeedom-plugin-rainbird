#!/usr/bin/env python3
from pyrainbird import RainbirdController
import time
import sys
import logging

logging.basicConfig(filename='pypython.log', level=logging.DEBUG)

_LOGGER = logging.getLogger(__name__)
_LOGGER.setLevel(logging.DEBUG)
ch = logging.StreamHandler()
ch.setLevel(logging.DEBUG)
formatter = logging.Formatter('%(asctime)s - %(name)s - %(levelname)s - %(message)s')
ch.setFormatter(formatter)
_LOGGER.addHandler(ch)

if len(sys.argv) < 5:
  print("Usage: irrigate_zone.py <ip> <password> <zone> <timer>")
  exit()

ip = sys.argv[1]
password = sys.argv[2]
zone = sys.argv[3]
timer = sys.argv[4]

controller = RainbirdController(ip, password)
controller.irrigate_zone(int(zone), timer)
