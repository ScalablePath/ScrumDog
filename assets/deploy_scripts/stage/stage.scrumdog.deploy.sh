#!/bin/bash

Mode="stage"

#First argument is the release to deploy (or blank to deploy trunk)
if [ $# -eq 1 ]
then
        RepoPath="${1}/deploy"
else
        echo "Please enter a location to deploy (e.g. branches/releases/1.0):"
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
ApacheDir="/home/fluide/public_html/${Mode}.scrumdog.com"
CurrentDir="/home/fluide/public_html/${Mode}.scrumdog/current"
WorkingDir="/home/fluide/public_html/${Mode}.scrumdog/working"
FilesDir="/home/fluide/public_html/${Mode}.scrumdog/uploads"
ReleasesDir="/home/fluide/public_html/${Mode}.scrumdog/releases"
ApacheUser="fluide"
ApacheGroup="fluide"
ReleasesToKeep=4



#================== Begin Deployment ==================



#Create the latest release directory
ReleaseTime=$(/bin/date "+%Y%m%d%H%M%S")
DeployDir="${ReleasesDir}/${ReleaseTime}"


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
cp -r ${WorkingDir} ${DeployDir}

#Overwrite the application config files
cp ${WebDir}/${Mode}.htaccess ${WebDir}/.htaccess

#Link the application files directory to the shared files directory
rm -rf ${WebDir}/uploads
ln -s ${FilesDir} ${WebDir}/uploads

#Change the ownership of the recently created files

#Set the permissions on the recently created files
chmod -R 775 ${CurrentDir}
chmod -R 775 ${DeployDir}
chmod -R 775 ${FilesDir}

#Build the model
#echo ">> Building the schema.yml file from the ${Mode} database..."
${DeployDir}/scrumdog/symfony doctrine:build-model
${DeployDir}/scrumdog/symfony doctrine:build-forms

#Run the consolidator
php ${DeployDir}/scrumdog/consolidator.php

#Clear the Symfony cache (not necessary since cache directory does not yet exist)
#${DeployDir}/${sfProjectName}/symfony cc

#remove the .svn files
#find ${DeployDir} -name "*.svn*" -exec rm -rf {} \;

#Point the current directory to our new release
rm -Rf ${ApacheDir}
ln -nfs ${WebDir} ${ApacheDir}

rm -Rf ${CurrentDir}
ln -nfs ${DeployDir} ${CurrentDir}

#Clear the application cache folder

#Remove the oldest releases directories
DirContents=$(ls -r ${ReleasesDir})
FileCount=0
for ReleaseDir in $DirContents
do
        let "FileCount += 1"
        if [ $FileCount -gt 1 ]
        then
                echo "Removing SVN files from ${ReleaseDir}..."
                rm -rf ${ReleasesDir}/${ReleaseDir}
        fi
        if [ $FileCount -gt $ReleasesToKeep ]
        then
                echo "Deleting ${ReleaseDir}..."
                rm -rf ${ReleasesDir}/${ReleaseDir}
        fi
done

#Log the
echo "${RepoUrl} was deployed to ${ReleasesDir} on `date`"