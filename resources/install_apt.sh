PROGRESS_FILE=/tmp/jeedom/rainbird/dependency

if [ ! -z $1 ]; then
    PROGRESS_FILE=$1
fi
touch ${PROGRESS_FILE}
echo 0 > ${PROGRESS_FILE}
echo "*************************************"
echo "*   Launch install of dependencies  *"
echo "*************************************"
echo $(date)
echo 5 > ${PROGRESS_FILE}
sudo apt-get clean
echo 10 > ${PROGRESS_FILE}
sudo apt-get autoremove
echo 15 > ${PROGRESS_FILE}
sudo apt-get update
echo 20 > ${PROGRESS_FILE}

echo "*****************************"
echo "Install modules using apt-get"
echo "*****************************"
sudo apt-get install -y python3-pip python3-setuptools
echo 48 > ${PROGRESS_FILE}


echo "*************************************"
echo "Install the required python libraries"
echo "*************************************"
sudo apt-get remove -y python3-crypto
echo 55 > ${PROGRESS_FILE}
sudo python3 -m pip uninstall -y pycryptodomex
echo 58 > ${PROGRESS_FILE}
sudo python3 -m pip install pycryptodome
echo 60 > ${PROGRESS_FILE}
sudo python3 -m pip install requests
echo 72 > ${PROGRESS_FILE}
sudo python3 -m pip install DateTime
echo 84 > ${PROGRESS_FILE}
sudo python3 -m pip install PyYAML
echo 90 > ${PROGRESS_FILE}
sudo python3 -m pip install setuptools
echo 90 > ${PROGRESS_FILE}

echo 100 > ${PROGRESS_FILE}
echo $(date)
echo "***************************"
echo "*      Install ended      *"
echo "***************************"
rm ${PROGRESS_FILE}
