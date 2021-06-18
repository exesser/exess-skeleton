[Back to index](index.md)

## Installation

Prerequisite: Make sure you have docker installed on your machine ([docker website](https://www.docker.com)).

- Run `composer create-project exesser/exess-skeleton my-project`
- Run `cd my-project`
- Run `make init`

This will setup the needed docker containers, fills the database with some test data, installs some git-hooks for the project

- Run `make test`, to run the php test suite and make sure everything's ok
- Run `make front-test`, to run the AngularJs test suite and make sure that's ok too
  
### Front-end

The front-end should be available at port 9005, so you should be able to visit [http://localhost:9005](http://localhost:9005) if npm is started (tip check docker logs for `cms-node` container)
