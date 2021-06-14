[Back to index](index.md)

## Installation

TODO: update these instructions !!!

Make sure you have docker installed on your machine.

Runt init (one time only)

```sh
cd dwp-temp
make init
```

Run tests:

```sh
make test
```

make a DB connection in your mysql client:

```sh
host: 127.0.0.1
database: exesscms 
port: 3306 
```

Open DWP:
[http://localhost:8888/](http://localhost:8888/)

### Configure PHPSTORM to Xdebug

![information](dev/docs/xdebug_settings.png)

And configure the Browser to start Xdebugging with PHPStorm by 
[generating the bookmarklets](https://www.jetbrains.com/phpstorm/marklets/) (click generate for xdebug,
and save those bookmarklets under a folder)

## Debug

If you are using chrome:

1. Install [Xdebug helper](https://chrome.google.com/webstore/detail/xdebug-helper/eadndfjplgieldjbigjakmdgkmoaaaoc)
2. Right-click on the Xdebug icon, select options and the proper IDE key
3. Left-click on the icon and select debug

If you are using Intellij/PHPStorm:

1. Go to preferences -> Languages & Frameworks -> PHP -> Servers
2. Create a new framework with the following options:
    - Host: `exesscms.dev`
    - Port: `80`
    - Debugger: `Xdebug`
    - For every route in your "Project Files" list, set `/vagrant` as your "Absolute path on the server" (this is a column)
3. Click in the "Start Listening for PHP Debug Connections".
4. Put a breakpoint somewhere.
5. Go to your local [DWP](http://exesscms.dev/dwp)
6. Now you can be a happy developer again!
