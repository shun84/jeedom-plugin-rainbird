#!/usr/bin/env python3
from pyrainbird import RainbirdController
import sys

if len(sys.argv) < 4:
  print("Usage: water_budget.py <ip> <password> <budget>")
  exit()

ip = sys.argv[1]
password = sys.argv[2]
budget = sys.argv[3]

controller = RainbirdController(ip, password)
controller.water_budget(budget)
