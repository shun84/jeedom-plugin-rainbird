PROGRESS_FILE=/tmp/jeedom/rainbird/dependency
BASEDIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

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
cd ${BASEDIR}/pyrainbird
sudo apt-get install -y python3-venv
python3 -m venv env
echo "Version de Python 3 installée dans venv :"
/env/bin/pip3 --version
echo 48 > ${PROGRESS_FILE}

echo "*************************************"
echo "Install the required python libraries"
echo "*************************************"
source env/bin/activate
pip3 install pycryptodome
echo 60 > ${PROGRESS_FILE}
pip3 install requests
echo 72 > ${PROGRESS_FILE}
pip3 install DateTime
echo 84 > ${PROGRESS_FILE}
pip3 install PyYAML
echo 86 > ${PROGRESS_FILE}
pip3 install setuptools
echo 88 > ${PROGRESS_FILE}
sudo apt-get install -y python3-crypto
echo 90 > ${PROGRESS_FILE}
deactivate

echo 100 > ${PROGRESS_FILE}
echo $(date)
echo "***************************"
echo "*      Install ended      *"
echo "***************************"
rm ${PROGRESS_FILE}
