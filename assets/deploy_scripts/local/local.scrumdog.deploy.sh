#!/bin/bash

Mode="dev"
#First argument is the release to deploy (or blank to deploy trunk)
if [ $# -eq 1 ]
then
        RepoPath="${1}/deploy"
else
        echo "Please enter a location to deploy (e.g. branches/releases 1.0):"
        read Location
		MyLocation="${Location}"
        if [ "${MyLocation}" == "" ]
        then
                echo "Setting location to trunk/deploy..."
                RepoPath="trunk/deploy"
        else
                RepoPath="$Location/deploy"
        fi
fi

RepoUrl="http://fluide.unfuddle.com/svn/fluide_scrumdog/${RepoPath}"
echo "About to deploy ${RepoUrl}"
RepoUser="deploy"
RepoPass="scrumbag7"
sfProjectName="scrumdog"
BaseDir="/Library/WebServer/Documents/local.scrumdog.com"
CurrentDir="${BaseDir}/current"
WorkingDir="${BaseDir}/working"
FilesDir="${BaseDir}/uploads"
ApacheUser="damien"
ApacheGroup="admin"

#================== Begin Deployment ==================

#Create the latest release directory
ReleaseTime=$(/bin/date "+%Y%m%d%H%M%S")
DeployDir="${CurrentDir}"

#Determine the Web Directory
WebDir=${DeployDir}/${sfProjectName}/web
EntriesFile="${WorkingDir}/.svn/entries"
if [ -f "$EntriesFile" ]
then
        WorkingRepo=$(sed -n '5,5p' $EntriesFile)
else
        WorkingRepo=""
fi
echo "Working repo is $WorkingRepo"
if [ "$WorkingRepo" == "$RepoUrl" ]
then
        echo "Updating working copy..."
        svn update --username ${RepoUser} --password ${RepoPass} ${WorkingDir}
else
        echo "Deleting the working copy and doing a full checkout..."
        rm -rf ${WorkingDir}
        mkdir ${WorkingDir}
        svn checkout --username ${RepoUser} --password ${RepoPass} ${RepoUrl} ${WorkingDir}
fi

#copy the working directory to the new release directory (also creates the deploy directory)
echo "Copying the working directory to the the release directory..."
rm -rf ${CurrentDir}
cp -r ${WorkingDir} ${DeployDir}

#Overwrite the application config files
#cp ${WebDir}/${Mode}.htaccess ${WebDir}/.htaccess

#Link the application files directory to the shared files directory
rm -rf ${WebDir}/uploads
ln -s ${FilesDir} ${WebDir}/uploads

#Change the ownership of the recently created files
#Set the permissions on the recently created files
chmod -R 770 ${CurrentDir}
chmod -R 770 ${DeployDir}
chmod -R 770 ${FilesDir}

#Build the model
${DeployDir}/${sfProjectName}/symfony doctrine:build-model
${DeployDir}/${sfProjectName}/symfony doctrine:build-forms

#Run the consolidator
#php ${DeployDir}/${sfProjectName}/consolidator.php #Don't want this in dev mode

#Log the
echo "${RepoUrl} was deployed to ${ReleasesDir} on `date`"