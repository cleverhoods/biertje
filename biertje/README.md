# Example work for Beer API consumer.

This project delivers 2 blocks. One for a daily random beer and another for an ajax search based on dish.

## Project setup
First you need to [install Lando](https://docs.lando.dev/basics/installation.html).

After that run the following command from the project root
```
lando start && lando site-install
```
> Note: for further information check the scripts/site-install.sh script.

When the installation script finishes copy the web/sites/default/default.services.yml to web/sites/default/services.yml and run.
```
lando drush cr
``` 

If you want/need to reinstall the site, just run
```
lando site-install
```
> Note: This will delete the current database.

## Usage
After installation visit the [Front page](https://biertje.lndo.site).

## Further development
- Add CircleCI and Pantheon support, see [example](https://github.com/lando/lando-pantheon-ci-workflow-example).
