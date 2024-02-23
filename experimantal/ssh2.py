#!/usr/bin/env python
import paramiko,sys,time,threading
from contextlib import contextmanager

host = sys.argv[1]
username = sys.argv[2]
password = sys.argv[3]

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
try:
  lock = threading.Lock()
  timeStart = time.time()
  print "creating connection"
  ssh.connect(host, username=username, password=password)
  print "connected"
  timeDone = time.time()
  time = timeDone-timeStart
  print "time: " + str(time)
except:
  timeDone = time.time()
  time = timeDone-timeStart
  print "time: " + str(time)
finally:
  print "closing connection"
  ssh.close()
  print "closed"
