# Console Bridge

# Content

- [Usage - how to use it](#usage)
- [Extensions - list of all extensions](#extension)
    - [Cache](#CacheConsole)
    - [Caching](#CachingConsole)
    - [DI](#DIConsole)
    - [Latte](#LatteConsole)
    - [Router](#RouterConsole)
    - [Security](#SecurityConsole)
    - [Utils](#UtilsConsole)
    
## Usage

**Register commands one by one:**

```yaml
extensions:
    console: Contributte\Console\DI\ConsoleExtension

    # register all console bridges
    console.extra: Contributte\Console\Extra\DI\ConsoleBridgesExtension

consol.extra
    # optionally disable these bridges
    cache: false
    caching: false
    di: false
    latte: false
    router: false
    security: false
    utils: false
```

You can also register bridges one by one.

```yaml
extensions:
    # register only bridges of your choice
    console.cache: Contributte\Console\Extra\DI\CacheConsoleExtension
    console.caching: Contributte\Console\Extra\DI\CachingConsoleExtension
    console.di: Contributte\Console\Extra\DI\DIConsoleExtension
    console.latte: Contributte\Console\Extra\DI\LatteConsoleExtension
    console.router: Contributte\Console\Extra\DI\RouterConsoleExtension
    console.security: Contributte\Console\Extra\DI\SecurityConsoleExtension
    console.utils: Contributte\Console\Extra\DI\UtilsConsoleExtension
```

To use these commands you gonna need to setu an **[bin/console entrypoint](https://github.com/contributte/console/blob/master/.docs/README.md#entrypoint)**.

## Extension

At this moment we have these bridges:

- cache
- caching
- di
- latte
- router
- security
- utils

### CacheConsole

```yaml
cache.console:
    purge: 
        - %tempDir%/cache
```

The `purge` parameter expects array of dirs.

Available commands:

- `nette:cache:purge`

### CachingConsole

Available commands:

- `nette:caching:clear`

    This command requires to specify the **cleaning strategy**. 

    The cleaning strategy options are:

    - `--all` or `-a` shortcut
    - `--tag <tag>` or `-t <tag>` shortcut
    - `--priority <priority>` or `-p <priority>` shortcut

    ***NOTE:** Only one tag can be used at the time.*

### DIConsole

```yaml
console.di:
    purge: 
        - %tempDir%/cache/Nette.Configurator
```

The `purge` parameter expects array of dirs.

Available commands:

- `nette:di:purge`

### LatteConsole

```yaml
console.latte:
    warmup: 
         - %tempDir%
    purge: 
         - %tempDir%/cache
```

The `warmup` and `purge` parameters are expecting array of dirs.

Available commands:

- `nette:latte:warmup` 
- `nette:latte:purge`

### RouterConsole

Available commands:

- `nette:router:dump`

### SecurityConsole

Available commands:

- `nette:security:password`

### UtilsConsole

Available commands:

- `nette:utils:random`

    This command supports count parameter (`--count <count>` or `-c <count>` shortcut), to change the count of random strings. Default count is **10**.
