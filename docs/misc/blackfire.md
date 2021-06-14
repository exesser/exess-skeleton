[Back to index](../index.md)

# Blackfire

For profiling our code we have the option to use blackfire. Blackfire is a tool for performance testing.
It will allow us to continiously verify and improve our app's performance,
by getting the right information at the right moment.

## Setting up Blackfire

First of al start by creating an account on [Blackfire](http://blackfire.io).

We've added a separate playbook to our ansible provisioning to allow enabling blackfire.
To install this playbook, in `vagrantfile.local` add or replace `$playbook` with the following value:
`playbook_blackfire.yml`. Also add a blackfire_server_id and blackfire_server_token,
you can find these in your account preferences on [Blackfire](http://blackfire.io).
Afterwards run `vagrant provision`. The difference between this playbook and the default one is,
that it actually disables xdebug and installs blackfire.

We now have a blackfire server up and running, on to installing the blackfire client on your local machine.
Follow the instructions for installing the CLI tool on [blackfire.io/docs/up-and-running/installation](https://blackfire.io/docs/up-and-running/installation)

On your local machine you should now be able to run the `blackfire` command in a terminal window.

## Using Blackfire

When you open your inspector in google chrome and open a link to the DWP, you will get a list of all api-calls
made. Right clicking on one of the calls will allow you to copy the request as cURL.

![copy as curl](resources/images/copy_as_curl.png "Copy as curl")

Now in your terminal type `blackfire`, paste the curl request, and enter.

Example input:

```
    blackfire curl 'http://localhost:9005/Api/V8_Custom/List/Accounts' -H 'Cookie: REST_API_TOKEN=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySWQiOiIxIiwiZXhwIjoxNDg0MzkxNjA5fQ.RArFj-nC1WsQgnLjYWBr93c6fNk2Ort-1T6bJUw1Z-8' -H 'Origin: http://localhost:9005' -H 'Accept-Encoding: gzip, deflate, br' -H 'X-LOG-COMPONENT: DWP' -H 'Accept-Language: en-US,en;q=0.8' -H 'X-LOG-ID: a8f65003-9d08-4b87-ac01-4c8675e2be5d' -H 'Content-Type: application/json;charset=UTF-8' -H 'X-LOG-DESCRIPTION: List: Accounts | URL: http://localhost:9005/Api/V8_Custom/List/Accounts' -H 'Accept: application/json, text/plain, */*' -H 'Referer: http://localhost:9005/' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.95 Safari/537.36' -H 'Connection: keep-alive' --data-binary '{"page":1}' --compressed
```

Example output:

```
    Profiling: [########################################] 10/10
    Blackfire cURL completed
    Graph URL https://blackfire.io/profiles/<some-unique-id/graph
    No tests! Create some now https://blackfire.io/docs/cookbooks/tests
    No recommendations

    Wall Time     3.61s
    CPU Time        n/a
    I/O Time        n/a
    Memory       26.7MB
    Network         n/a     n/a       -
    SQL             n/a       -
```

If you then open the graph url you can see something like this on which you can further profile your call:

![Blackfire graph](resources/images/blackfire_graph.png "Blackfire graph")
