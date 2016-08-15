# $1: git tag
echo "$1" | grep -q ""^[0-9]+\.[0-9]+\.[0-9]+"
if [  $? -eq 0  ] ;then
echo "Release $1"
git config --global user.email "builds@travis-ci.com"
git config --global user.name "Travis CI"
git tag $1 -a -m "Generated tag $1 from TravisCI build $TRAVIS_BUILD_NUMBER"
git push origin $GIT_TAG
else
  echo "Not a release: $1"
fi