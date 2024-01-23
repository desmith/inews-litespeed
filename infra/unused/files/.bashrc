# .bashrc
# Source global definitions
if [ -f /etc/bashrc ]; then
	. /etc/bashrc
fi

if [ -f ~/.aliases ]; then
	. ~/.aliases
fi

profile() {
    export AWS_PROFILE=$1
    aws sso login --profile $1
    yawsso -p $1
}

export PATH=$PATH:/usr/local/bin

# Uncomment the following line if you don't like systemctl's auto-paging feature:
# export SYSTEMD_PAGER=

# Black       0;30     Dark Gray    1;30
# Blue        0;34     Bold Blue    1;34
# Green       0;32     Bold Green   1;32
# Cyan        0;36     Bold Cyan    1;36
# Red         0;31     Bold Red     1;31
# Purple      0;35     Bold Purple  1;35
# Brown       0;33     Yellow       1;33
# Light Gray  0;37     White        1;37

# User specific aliases and functions
# Colour codes are cumbersome, so let's name them
#txtcyn='\[\e[0;96m\]' # Cyan
txtcyn='\[\e[0;36m\]' # Cyan
txtgrn='\[\e[0;32m\]' # Cyan
txtyel='\[\e[0;33m\]' # Cyan
txtblu='\[\e[0;34m\]' # Cyan
txtpur='\[\e[0;35m\]' # Purple
txtwht='\[\e[0;37m\]' # White
txtrst='\[\e[0m\]'    # Text Reset

# Which (C)olour for what part of the prompt?
pathC="${txtblu}"
hostC="${txtyel}"
userC="${txtgrn}"
gitC="${txtpur}"
pointerC="${txtwht}"
normalC="${txtrst}"
# Get the name of our branch and put parenthesis around it
gitBranch() {
	git branch 2>/dev/null | sed -e '/^[^*]/d' -e 's/* \(.*\)/(\1)/'
}
# Build the prompt
export PS1="${userC}\u${txtwht}@${hostC}\h:${pathC}\w ${gitC}\$(gitBranch) ${pointerC}\n\t \$${normalC} "

BR_APP="${HOME}/.config/broot/launcher/bash/br"
[ -f "{BR_APP}" ] && source ${BR_APP}

[ -f ~/.fzf.bash ] && source ~/.fzf.bash

WP_COMPLETIONS="${HOME}/bin/completions/wp-cli.completions"
[ -f "${WP_COMPLETIONS}" ] && source ${WP_COMPLETIONS}
