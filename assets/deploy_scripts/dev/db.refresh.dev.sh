#!/bin/bash
CurrentTime=$(/bin/date "+%Y%m%d%H%M%S")
DumpName="dev_${CurrentTime}.sql"
echo ">> Dumping prod database to ${DumpName} ..."
/usr/bin/mysqldump --user=fluide_scrumdog --password=agile888 --routines fluide_scrumdog > ${DumpName}
echo ">> Importing ${DumpName} ..."
mysql -ufluide_devscru -pagile111 fluide_devscrum < ${DumpName}
rm ${DumpName}
echo ">> DB Refresh complete."