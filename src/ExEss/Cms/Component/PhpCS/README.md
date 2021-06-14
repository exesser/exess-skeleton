# PhpCS Component

## What

This folder holds our project its PHPCS configuration

## How

* The folder holds the custom sniffs in the Nova namespace / folder.
* phpcs.dist.xml is the template file which will be used to provision
a phpcs.host.xml file which is needed to run the githooks from your 
host system (outside vagrant)
* phpcs.xml is used by the vagrant machine to run phpcs


