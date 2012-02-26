#!/bin/bash
Mode="stage"
ProdFilesDir="/home/fluide/www/prod.scrumdog/uploads"
DestFilesDir="/home/fluide/www/${Mode}.scrumdog/uploads"
rm -rf ${DestFilesDir}
cp -R ${ProdFilesDir} ${DestFilesDir}
echo ">> File Refresh complete."