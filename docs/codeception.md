[Back to index](index.md)

# Codeception

## Running tests

All commands to performed from the root folder (`/var/www/crm/nova-crm/` in most cases).

Run all tests (phpunit and codeception):

`./runtests`

Run a specific suite:

`bin/codecept run api`

Run all tests in a specific folder:

`bin/codecept run tests/api/V8_Custom/Flow/CUPQ`

Run a specific test:

`bin/codecept run tests/api/V8_Custom/Flow/CUPQ/CUPQMultiEANCest.php`

Run a specific test with full debug output (very practical for api tests!):

`bin/codecept run tests/api/V8_Custom/Flow/CUPQ/CUPQMultiEANCest.php -vvv`

## Modules

### Vcr

This is a custom made module, implementing [php-vcr](http://php-vcr.github.io/>) as a module.

The purpose of this module is to record all calls to external systems during development, so these recordings can be 'played back' when the tests are ran on other systems (such as build servers). This effectively eliminates the need to deploy these external systems upon build and it makes sure your build will not fail when doing functional tests.

For each of your tests, a 'cassette' will be created in ``tests/_vcr``. In theses 'cassettes' an 'episode' is recorded for each external request that occurs during your functional test. This episodes contains request headers, url and body, together with the response headers and body. This enables the complete request to be emulated when a 'cassette' is 'played back'.

Some remarks:

* If you call an external system to 'create' something, and the response contains some sort of 'id', you will see that the 'cassette' has an episode added for each call. This is not unusual, however make sure you do not commit every call, one request and one response is enough.
* You can play with the ``record`` and ``playback`` settings in ``codeception.yml`` to test if playback works for your specific test. If you first do the call with ``record: true`` and ``playback: false``, your test should also work with ``record: false`` and ``playback: true``.

### Slim

This is a custom module, which enables to route requests directly into Slim Framework, without the need for a working web server.

This enables us to route calls directly (practical for api tests), and to access services on the bootstrapped framework. The latter is practical for functional tests, where you typically want to test a service which has all of its dependencies injected in it (without the need to set all these up yourself to test).

### Db

The module is the standard Codeception [Db Module](http://codeception.com/docs/modules/Db)

Some important notes about this module:

* Whenever a suite is started, the dump in `tests/_data/dump.sql` is being reloaded in a test database.
* During your tests in that specific suite, the dump is not being reloaded, so when you change/insert data during a test, it stays there unless you remove it with an _after hook.
* When the database structure has changed, and you need the new structure in tests, you have to update the dump so it reflects the new situation. A shell script to do this was created, just go to `tests/` and run `./create-local-sql-dump.sh`

