# Console Bridge

# Content

- [Usage - how to use it](#usage)
- [Extensions - list of all extensions](#extension)
    - [Cache](#cacheconsole)
    - [Caching](#cachingconsole)
    - [DI](#diconsole)
    - [Latte](#latteconsole)
    - [Router](#routerconsole)
    - [Security](#securityconsole)
    - [Utils](#utilsconsole)
    - [Advanced cache](#advancedcacheconsole)

## Usage

**Register commands one by one:**

```yaml
extensions:
    console: Contributte\Console\DI\ConsoleExtension

    # register all console bridges
    console.extra: Contributte\Console\Extra\DI\ConsoleBridgesExtension

console.extra
    # optionally disable these bridges
    cache: false
    caching: false
    di: false
    latte: false
    router: false
    security: false
    utils: false
    advancedCache: false
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
    console.advancedCache: Contributte\Console\Extra\DI\AdvancedCacheConsoleExtension
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
- advanced cache

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

### AdvancedCacheConsole

#### Generator

Generate application cache with single command

- `contributte:cache:generate`

    `--list` show list of available generators

    `--generator=GENERATOR` use only specified generator

##### Register generators you want to use:

```yaml
console.advancedCache
    generators:
        latte: Contributte\Console\Extra\Generators\LatteTemplatesCacheGenerator(
            [%appDir%],
            @Nette\Bridges\ApplicationLatte\ILatteFactory::create()
        )
```

##### Available generators:

- Latte templates cache generator

    ```yaml
    Contributte\Console\Extra\Generators\LatteTemplatesCacheGenerator(
        [%appDir%],
        @Nette\Bridges\ApplicationLatte\ILatteFactory::create()
    )
    ```

- DI containers generator

    - This example is configured to generate 3 containers - 1 for production mode, 1 for debug mode and 1 for console (should be enough for every application)
    - You don't need to add `productionMode` parameter for Nette BC, it is done automatically.

    ```yaml
    Contributte\Console\Extra\Generators\DiContainersCacheGenerator(
        [
            debug: [debugMode: true, consoleMode: false],
            production: [debugMode: false, consoleMode: false],
            console: [debugMode: true, consoleMode: true]
        ],
        "?->getService('configurator')"(@container)
    )
    ```

    You will also need slightly modify `bootstrap.php` to get this generator work.

    ```php
    $configurator->addServices(['configurator' => $configurator]); // we need Configurator available as a service
    $container = $configurator->createContainer();
    return $container;
    ```

##### Implement your own generator:

```php
use Contributte\Console\Extra\Cache\Generators\IGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class YourGenerator implements IGenerator
{

    public function getDescription(): string
    {
        return 'description which is shown in console when you run `contributte:cache:generate --list`'
    }

    public function generate(InputInterface $input, OutputInterface $output): bool
    {
        // generate cache
        // inform about it in console
        // return true if generating was successful, false otherwise
    }

}
```

#### Cleaner

Clean application cache with single command

- `contributte:cache:clean`

    `--list` show list of available cleaners

    `--cleaner=CLEANER` use only specified cleaner

##### Register cleaners you want to use:

```yaml
console.advancedCache
    cleaners:
        localFs: Contributte\Console\Extra\Cleaners\LocalFilesystemCleaner([%tempDir%])
```

##### Available cleaners:

 - APC cleaner

    ```yaml
    Contributte\Console\Extra\Cache\Cleaners\ApcCleaner()
    ```

- APCu cleaner

    ```yaml
    Contributte\Console\Extra\Cache\Cleaners\ApcuCleaner()
    ```

- Local filesystem cleaner

    ```yaml
    Contributte\Console\Extra\Cache\Cleaners\LocalFilesystemCleaner([%tempDir%], [%tempDir%/ignored/])
    ```

- Memcache(d) cleaner

    ```yaml
    Contributte\Console\Extra\Cache\Cleaners\MemcachedCleaner([@memcache1, @memcache2])
    ```

- Nette\Caching\IStorage cleaner

    ```yaml
    Contributte\Console\Extra\Cache\Cleaners\NetteCachingStorageCleaner([@storage1, @storage2])
    ```

- Opcode cleaner

    ```yaml
    Contributte\Console\Extra\Cache\Cleaners\OpcodeCleaner()
    ```

##### Implement your own cleaner:

```php
use Contributte\Console\Extra\Cache\Cleaners\ICleaner;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class YourCleaner implements ICleaner
{

    public function getDescription(): string
    {
        return 'description which is shown in console when you run `contributte:cache:clean --list`'
    }

    public function clean(InputInterface $input, OutputInterface $output): bool
    {
        // clean cache
        // inform about it in console
        // return true if cleaning was successful, false otherwise
    }

}
```
