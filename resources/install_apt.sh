#!/bin/bash

PROGRESS_FILE=/tmp/jeedom/rainbird/dependance
touch ${PROGRESS_FILE}
echo 0 > ${PROGRESS_FILE}
echo "********************************************************"
echo "*             Installation des dépendances             *"
echo "********************************************************"

BASEDIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

function apt_install {
  sudo apt-get -y install "$@"
  if [ $? -ne 0 ]; then
    echo "could not install $1 - abort"
    rm ${PROGRESS_FILE}
    exit 1
  fi
}

function pip_install {
  sudo python3 -m pip install "$@"
  if [ $? -ne 0 ]; then
    echo "could not install $p - abort"
    rm ${PROGRESS_FILE}
    exit 1
  fi
}

echo "Version de Python 3 installée :"
sudo python3 --version
echo 12 > ${PROGRESS_FILE}
sudo rm -f /var/lib/dpkg/updates/*
sudo apt-get clean
echo 24 > ${PROGRESS_FILE}
sudo apt-get update
echo 36 > ${PROGRESS_FILE}
echo "Installation des dependances"
apt_install python3-pip python3-setuptools
echo 48 > ${PROGRESS_FILE}
echo "Installation des dependances Python 3"
pip_install pycryptodomex
echo 60 > ${PROGRESS_FILE}
pip_install requests
echo 72 > ${PROGRESS_FILE}
pip_install datetime
echo 84 > ${PROGRESS_FILE}
echo 100 > ${PROGRESS_FILE}
echo "********************************************************"
echo "*             Installation terminée                    *"
echo "********************************************************"
rm ${PROGRESS_FILE}
