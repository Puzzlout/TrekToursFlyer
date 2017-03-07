# Copy the vendor assets retrieve with Bower to the AppBundle resources.
# This is necessary in order to install without Bower.
#!/usr/bin/env bash

cp web/assets/vendor/bootstrap/dist/css/bootstrap.min.css 	src/AppBundle/Resources/public/vendor/bootstrap.min.css
cp web/assets/vendor/font-awesome/css/font-awesome.min.css 	src/AppBundle/Resources/public/vendor/font-awesome.min.css
cp web/assets/vendor/jquery/dist/jquery.min.js 				src/AppBundle/Resources/public/vendor/jquery.min.js
cp web/assets/vendor/bootstrap/dist/js/bootstrap.min.js 	src/AppBundle/Resources/public/vendor/bootstrap.min.js
