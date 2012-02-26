#!/bin/bash

#First argument is the release to deploy (or blank to deploy trunk)
if [ $# -eq 1 ]
then
        RepoPath="${1}"
else
        RepoPath=""
fi

Mode="stage"
RepoUrl="http://fluide.unfuddle.com/svn/fluide_scrumdog/${RepoPath}/assets/deploy_scripts/${Mode}"
RepoUser="deploy"
RepoPass="scrumbag7"

AdminScriptsDir="/home/fluide/public_html/${Mode}.scrumdog/admin_scripts/${Mode}"
TriggerFile="trigger.${Mode}.sh"
DeployFile="${Mode}.scrumdog.deploy.sh"

#================== Begin Deployment ==================

#delete the local files
rm ${TriggerFile}
rm ${DeployFile}

#Update the working copy
svn update --username ${RepoUser} --password ${RepoPass} ${AdminScriptsDir}

echo "updated admin scripts directory"

echo "running dos2unix"
dos2unix ${TriggerFile} ${TriggerFile}
dos2unix ${DeployFile} ${DeployFile}

#Set the permissions on the recently created files
echo "set permissions"
chmod -R 775 ${AdminScriptsDir}

echo "calling deploy script"
${AdminScriptsDir}/${Mode}.scrumdog.deploy.sh ${RepoPath}

echo "deploy completed."
