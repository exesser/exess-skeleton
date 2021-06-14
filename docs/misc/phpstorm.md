[Back to index](../index.md)

# PHPStorm

## Live templates

A couple of handy Live Templates for PHPStorm. To install copy the xml,
go to `Preferences` > `Editor` > `Live templates`; press the `+` sign and add a group called CRM.

Right click the group and select `paste`.

included live templates:

```
    /* config[TAB] -> */   Container(/*TAB*/::class)/*TAB*/
    /* mock[TAB] -> */     \Mockery::mock(/*TAB*/::class)/*TAB*/
```

### XML

```
    <template name="config" value="Container::get($CLASS$::class)$END$" description="Container::get(::class)" toReformat="false" toShortenFQNames="true">
      <variable name="CLASS" expression="" defaultValue="" alwaysStopAt="true" />
      <context>
        <option name="PHP Expression" value="true" />
        <option name="PHP Statement" value="true" />
      </context>
    </template>
    <template name="mock" value="\Mockery::mock($CLASS$::class)$END$" description="Mockery::mock(::class)" toReformat="false" toShortenFQNames="true">
      <variable name="CLASS" expression="" defaultValue="" alwaysStopAt="true" />
      <context>
        <option name="PHP Expression" value="true" />
        <option name="PHP Statement" value="true" />
      </context>
    </template>
```
