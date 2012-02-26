#!/bin/bash
CurrentTime=$(/bin/date "+%Y%m%d%H%M%S")
DumpName="stage_${CurrentTime}.sql"
echo ">> Dumping prod database to ${DumpName} ..."
/usr/bin/mysqldump --user=fluide_scrumdog --password=agile888 --routines fluide_scrumdog > ${DumpName}
echo ">> Importing ${DumpName} ..."
mysql -ufluide_stagesc -pagile222 fluide_stagescrum < ${DumpName}
rm ${DumpName}
echo ">> Refresh complete."