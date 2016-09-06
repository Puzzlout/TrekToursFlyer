if [ "$1" != "" ]
then
  echo "Tagging commit $2"
  git tag -a $1 $2 -m "Release of $1"
else
  echo "Tagging current commit"
  git tag -a $1 -m "Release of $1"
fi
git push origin $1
