# Copy the vendor assets retrieve with Bower to the AppBundle resources.
# This is necessary in order to install without Bower.

rm -R src/AppBundle/Resources/public/vendor/*
cp -R web/assets/vendor/* src/AppBundle/Resources/public/vendor/
