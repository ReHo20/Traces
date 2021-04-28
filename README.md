# Base Project

## Setup
1. Copy `.env.example` to `.env` and setup your settings
2. Run `composer install` in the project root
3. Generate the keys at [roots.io](https://roots.io/salts.html)
4. Run the wordpress installer as usual
5. Optional: Enable the Dutch language in the Wordpress settings
6. Done

## Updating
To update wordpress simply change the wordpress version in the `composer.json` and run `composer update`. I recommend sticking to the same version as the base project.

## Noteworthy:
To make the htaccess work on Windows replace 
`RewriteRule (.*) /web/$1 [L]` with `RewriteRule (.*) web/$1 [L]`

When moving to a production host be sure to set the environment to `production` to allow for the indexing of the site.
