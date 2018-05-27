# Console Bridge

# Content
- [Usage - how to use it](#usage)
    - [Register one by one](#usage)
    - [Register ConsoleBridge](#usage)
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
    cacheConsole: Contributte\Console\Extra\DI\CacheConsoleExtension
    cachingConsole: Contributte\Console\Extra\DI\CachingConsoleExtension
    diConsole: Contributte\Console\Extra\DI\DIConsoleExtension
    latteConsole: Contributte\Console\Extra\DI\LatteConsoleExtension
    routerConsole: Contributte\Console\Extra\DI\RouterConsoleExtension
    securityConsole: Contributte\Console\Extra\DI\SecurityConsoleExtension
    utilsConsole: Contributte\Console\Extra\DI\UtilsConsoleExtension
```
You can install just one or multiple, but if you want all, we recommend **ConsoleBridge**.


**Register ConsoleBridge:**
```yaml
exntesions:
    consoleBridge: Contributte\Console\Extra\DI\consoleBridgeExtension
```

The ConsoleBridge registers all the extensions automatically, you just have to register the `ConsoleBridgeExtension`

To use these commands we recommend to use an **[entrypoint](https://github.com/contributte/console/blob/master/.docs/README.md#entrypoint)** or you can use your `index.php`.

With [entrypoint](https://github.com/contributte/console/blob/master/.docs/README.md#entrypoint):
```bash
./bin/console <command>
```
without [entrypoint](https://github.com/contributte/console/blob/master/.docs/README.md#entrypoint):
```bash
php public/index.php <command>
```

## Extension

**All extensions does not require any configuration, the values are the default ones except the RouterConsole and CachingConsole extensions, they get the services from DI container.**

### CacheConsole
**Standalone configuration:**
```yaml
cacheConsole:
    purge: 
        - %tempDir%/cache
```
**consoleBridge configuration:**
```yaml
consoleBridge:
    cache:
        purge: 
            - %tempDir%/cache
            
    # Or you can disable this extension
    cache: false
```

The command name is `nette:cache:purge`, and you can execute it like this:
```bash
./bin/console nette:cache:purge
```


### CachingConsole
**Standalone configuration:**
```yaml
cachingConsole:
    clear: @caching.storage
```
**consoleBridge configuration:**
```yaml
consoleBridge:
    caching:
        clear: @caching.storage
        
    # Or you can disable this extension
    caching: false
```

The `clear` parameter accepts only classes that implement `Nette\Caching\IStorage` interface.

The command name is `nette:caching:clear`, and you can execute it like this:
```bash
./bin/console nette:caching:clear
```

This command requires to specify the **cleaning strategy**. 


The cleaning strategy options are:
- `--all` or `-a` shortcut
- `--tag <tag>` or `-t <tag>` shortcut
- `--priority <priority>` or `-p <priority>` shortcut

***NOTE:** Only one tag can be used at the time.*

### DIConsole
**Standalone configuration:**
```yaml
diConsole:
    purge: 
        - %tempDir%/cache/Nette.Configurator
```
**consoleBridge configuration:**
```yaml
consoleBridge:
    di:
        purge: 
            - %tempDir%/cache
            
    # Or you can disable this extension
    di: false
```

The `purge` parameter expects array of dirs.

The command name is `nette:di:purge`, and you can execute it like this:
```bash
./bin/console nette:di:purge
```

### LatteConsole
**Standalone configuration:**
```yaml
latteConsole:
    warmup: 
         - %tempDir%
    purge: 
         - %tempDir%/cache
```
**consoleBridge configuration:**
```yaml
consoleBridge:
    latte:
        warmup: 
            - %tempdir%
        purge: 
            - %tempDir%/cache
            
    # Or you can disable this extension
    latte: false
```
The `warmup` and `purge` parameters are expecting array of dirs.

The command name is `nette:latte:warmup` and `nette:latte:purge`, and you can execute it like this:
```bash
./bin/console nette:latte:warmup
./bin/console nette:latte:purge
```

### RouterConsole
**Standalone configuration:**
```yaml
routerConsole:
    dump: @router
```
**consoleBridge configuration:**
```yaml
consoleBridge:
    router:
        dump: @router
        
    # Or you can disable this extension
    router: false
```

The `dump` parameter accepts only classes that implement `Nette\Application\IRouter` interface.

The command name is `nette:router:dump`, and you can execute it like this:
```bash
./bin/console nette:router:dump
```

### SecurityConsole
**Standalone configuration:**

_This extension has no configuration._

**consoleBridge configuration:**
```yaml
consoleBridge:
    # You can disable this extension:
    security: false
```

The command name is `nette:security:password`, and you can execute it like this:
```bash
./bin/console nette:security:password
```

### UtilsConsole
**Standalone configuration:**

_This extension has no configuration._

**consoleBridge configuration:**
```yaml
consoleBridge:
    # You can disable this extension:
    utils: false
```

The command name is `nette:utils:random`, and you can execute it like this:
```bash
./bin/console nette:utils:random
```

This command supports count parameter (`--count <count>` or `-c <count>` shortcut), to change the count of random strings. Default count is **10**.
