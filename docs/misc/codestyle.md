[Back to index](../index.md)

# Code Style

The coding standard for this project is PSR-2.
Any new code must follow the coding standard. This is enforced with a pre-commit hook.

The configuration of which rules are enforced can be found in `/config/phpcs.host.xml`

## Fixing errors

The code sniffer also has the ability to fix some errors automatically. The command used for this is `phpcfb`

e.g.: to fix errors in `custom/MyClass.php`

```
   docker-compose exec -T php bin/phpcbf --standard=config/phpcs.host.xml custom/MyClass.php
```

## PHPStorm

PHPStorm can fix almost all code style errors automatically.

[download PHP code style for PHPStorm](../resources/exess_code_style.xml)

### PHPStorm Additional Inspections

In addition to the code style configuration, phpstorm can be configured to use the code sniffer configuration for
additional inspections. These additional inspections give you the added benefit of being able to define more strict
rules which are agreed upon by the team. PHP compatibility checks can also be added. All these inspections will be
enforced by the pre-commit hook.

* Add a remote (vagrant) interpreter to PHPStorm: `Preferences -> Languages & Frameworks -> PHP`
* Configure the codesniffer path: `Preferences -> Languages & Frameworks -> PHP -> PHP Quality Tools -> PHP_CodeSniffer`
* Enable the php code sniffer inspection: `Preferences -> Editor -> Inspections`
  The path for the custom coding standard ruleset is `config/phpcs.host.xml`
