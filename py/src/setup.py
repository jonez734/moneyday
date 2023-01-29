#!/usr/bin/env python3

from setuptools import setup

import time

v = time.strftime("%Y%m%d%H%M")
projectname = "moneyday"

setup(
  name=projectname,
  version=v,
  author="zoidtechnologies.com",
  author_email="%s@projects.zoidtechnologies.com" % (projectname),
  license="GPLv3",
  scripts=["../bin/moneyday"],
  requires=["ttyio5", "bbsengine5"],
  packages=["moneyday"],
  url="https://projects.zoidtechnologies.com/%s/" % (projectname),
  classifiers=[
    "Programming Language :: Python :: 3.10",
    "Environment :: Console",
    "Development Status :: 5 - Production/Stable",
    "Intended Audience :: Developers",
    "Operating System :: POSIX",
    "Topic :: Software Development :: Libraries :: Python Modules",
    "Topic :: Terminals",
    "License :: OSI Approved :: GNU General Public License v3 (GPLv3)",

  ],
)
