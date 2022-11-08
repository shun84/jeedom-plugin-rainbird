#!/usr/bin/env python3
from pyrainbird import RainbirdController
import sys

if len(sys.argv) < 4:
  print("Usage: set_program.py <ip> <password> <numbprog>")
  exit()

ip = sys.argv[1]
password = sys.argv[2]
numbprog = sys.argv[3]

controller = RainbirdController(ip, password)
controller.set_program(numbprog)