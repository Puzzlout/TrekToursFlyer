#$1 dev or prod
mkdir ../deploy
cd ../deploy
git clone https://github.com/Puzzlout/CloudDeploy.git
sh deploy.sh
sh Projects/TrekTours/install.sh $1
sh Projects/TrekTours/refresh.sh $1
