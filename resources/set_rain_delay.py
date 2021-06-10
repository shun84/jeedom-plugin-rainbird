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

if len(sys.argv) < 4:
  print("Usage: set_rain_delay.py <ip> <password> <days>")
  exit()

ip = sys.argv[1]
password = sys.argv[2]
days = sys.argv[3]

controller = RainbirdController(ip, password)
controller.set_rain_delay(days)
