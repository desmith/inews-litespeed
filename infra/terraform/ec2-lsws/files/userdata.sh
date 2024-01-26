#!/usr/bin/env bash
(
    set -x

    echo "****************** DEBUG VARS **********************"
    echo "app: ${app}"
    echo "component: ${component}"
    echo "env: ${env}"
    echo "playbook_url: ${playbook_url}"
    echo "***************** END DEBUG VARS ********************"

    # download the playbook and unpack it
    mkdir -p /opt/ansible/deploy
    #aws s3 cp "${playbook_url}" /opt/ansible/deploy/deploy.zip
    cd /opt/ansible/deploy || exit
    #unzip deploy.zip

    # set environment
    export PYENV_ROOT="/root/.pyenv"
    export PATH="$PYENV_ROOT/bin:$PATH"
    export PIPENV_YES=true
#    eval "$(pyenv init -)"
#    eval "$(pyenv virtualenv-init -)"

    #++ pyenv init -
    #/var/lib/cloud/instance/scripts/part-001: line 26: pyenv: command not found
    #+ eval ''
    #++ pyenv virtualenv-init -
    #/var/lib/cloud/instance/scripts/part-001: line 27: pyenv: command not found
    #+ eval ''
    #+ pipenv install --deploy
    #/var/lib/cloud/instance/scripts/part-001: line 30: pipenv: command not found

    # run the playbook
    #pipenv install --deploy
    #pipenv run ansible-playbook ansible_playbook.yml -e component=${component}

) 2>&1 | tee -a /var/log/bootstrap.log
# Capture the success of the subshell, not the tee command.
echo $${PIPESTATUS[0]} > /tmp/bootstrap.status
