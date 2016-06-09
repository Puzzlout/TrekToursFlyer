# TrekToursFlyer [![Build Status](https://travis-ci.org/Puzzlout/TrekToursFlyer.svg?branch=master)](https://travis-ci.org/Puzzlout/TrekToursFlyer) [![codecov](https://codecov.io/gh/Puzzlout/TrekToursFlyer/branch/master/graph/badge.svg)](https://codecov.io/gh/Puzzlout/TrekToursFlyer) [![Codacy Badge](https://api.codacy.com/project/badge/Grade/2decfe7a882545048474071f3be171be)](https://www.codacy.com/app/Puzzlout/TrekToursFlyer?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Puzzlout/TrekToursFlyer&amp;utm_campaign=Badge_Grade)

This is the Trek tours business flyer built with Symphony 3

# Minimum requirements

- You must have a Apache 2 server running.
- You must have a running PHP 7 server.
- You must have a MYSQL 5 version install.
- You must have Git installed locally.
- You must have Composer installed locally.
- You are recommended to use an advanced IDE such as Netbeans or PhpStorm

# Contributing

This is a private project. Contributing is not open except for Puzzlout team members.

# Installation

Run the following commands to get started:
- `git clone https://github.com/Puzzlout/TrekToursFlyer.git`
- `cd TrekToursFlyer`
- Setup your localhost to listen to port 80.
- Point Apache document root to /path/to/repo/TrekToursFlyer/web
- Setup MySql server to listen to port 3306.
- Setup your vshost.conf with the following:
```
<VirtualHost trektoursflyer.dev>
    ServerName trektoursflyer.dev
    DocumentRoot "/www/sites/TrekToursFlyer/web"
    <Directory "/www/sites/TrekToursFlyer/web">
        RewriteEngine On
        RewriteBase /
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^(.*)$ app_dev.php [L,QSA]
        AllowOverride None
    </Directory>
    AddType application/vnd.ms-fontobject    .eot
    AddType application/x-font-opentype      .otf
    AddType image/svg+xml                    .svg
    AddType application/x-font-ttf           .ttf
    AddType application/font-woff            .woff
</VirtualHost>
```

- In Terminal, run the following to install the application dependencies: `sh sh/install.sh` and then `sh sh/refresh.sh dev`
- Launch your web server and go to http://trektoursflyer.dev/. You should see the homepage.
- If you don't see the homepage, check http://trektoursflyer.dev/config.php for any warnings or errors.
