![](https://heatbadger.now.sh/github/readme/contributte/console-extra/)

<p align=center>
  <a href="https://github.com/contributte/console-extra/actions"><img src="https://badgen.net/github/checks/contributte/console-extra/master?cache=300"></a>
  <a href="https://coveralls.io/r/contributte/console-extra"><img src="https://badgen.net/coveralls/c/github/contributte/console-extra?cache=300"></a>
  <a href="https://packagist.org/packages/contributte/console-extra"><img src="https://badgen.net/packagist/dm/contributte/console-extra"></a>
  <a href="https://packagist.org/packages/contributte/console-extra"><img src="https://badgen.net/packagist/v/contributte/console-extra"></a>
</p>
<p align=center>
  <a href="https://packagist.org/packages/contributte/console-extra"><img src="https://badgen.net/packagist/php/contributte/console-extra"></a>
  <a href="https://github.com/contributte/console-extra"><img src="https://badgen.net/github/license/contributte/console-extra"></a>
  <a href="https://bit.ly/ctteg"><img src="https://badgen.net/badge/support/gitter/cyan"></a>
  <a href="https://bit.ly/cttfo"><img src="https://badgen.net/badge/support/forum/yellow"></a>
  <a href="https://contributte.org/partners.html"><img src="https://badgen.net/badge/sponsor/donations/F96854"></a>
</p>

<p align=center>
Website 🚀 <a href="https://contributte.org">contributte.org</a> | Contact 👨🏻‍💻 <a href="https://f3l1x.io">f3l1x.io</a> | Twitter 🐦 <a href="https://twitter.com/contributte">@contributte</a>
</p>

Nette-based console commands for Latte, DIC, security, utilities, database backups and cache maintenance.

## Versions

| State       | Version | Branch   | Nette | PHP     |
|-------------|---------|----------|-------|---------|
| dev         | `^0.10` | `master` | 3.2+  | `>=8.2` |
| stable      | `^0.9`  | `master` | 3.2+  | `>=8.2` |

## Contents

- [Installation](#installation)
- [Extensions](#extensions)
  - [Cache](#cacheconsole)
  - [Caching](#cachingconsole)
  - [DI](#diconsole)
  - [Latte](#latteconsole)
  - [Router](#routerconsole)
  - [Security](#securityconsole)
  - [Utils](#utilsconsole)
  - [Database](#database)
  - [AdvancedCache](#advancedcacheconsole)
- [Compatibility](#compatibility)
  - [Kdyby/Console](#kdybyconsole)
- [Examples](#examples)

## Installation

To install latest version of `contributte/console-extra` use [Composer](https://getcomposer.org).

```bash
composer require contributte/console-extra
```

Register all commands

```neon
extensions:
	console: Contributte\Console\DI\DIConsoleExtension(%consoleMode%)

	# register all console bridges
	console.extra: Contributte\Console\Extra\DI\ConsoleBridgesExtension(%consoleMode%)

console.extra:
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

You can also register bridges one by one

```neon
extensions:
	# register only bridges of your choice
	console.cache: Contributte\Console\Extra\DI\CacheConsoleExtension(%consoleMode%)
	console.caching: Contributte\Console\Extra\DI\CachingConsoleExtension(%consoleMode%)
	console.di: Contributte\Console\Extra\DI\DIConsoleExtension(%consoleMode%)
	console.latte: Contributte\Console\Extra\DI\LatteConsoleExtension(%consoleMode%)
	console.router: Contributte\Console\Extra\DI\RouterConsoleExtension(%consoleMode%)
	console.security: Contributte\Console\Extra\DI\SecurityConsoleExtension(%consoleMode%)
	console.utils: Contributte\Console\Extra\DI\UtilsConsoleExtension(%consoleMode%)
	console.advancedCache: Contributte\Console\Extra\DI\AdvancedCacheConsoleExtension(%consoleMode%)
	console.database: Contributte\Console\Extra\DI\DatabaseConsoleExtension(%consoleMode%)
```

To use these commands you need to setup a **[bin/console entrypoint](https://github.com/contributte/console#entrypoint)**.

## Extensions

At this moment, these bridges are available:

- cache
- caching
- di
- latte
- router
- security
- utils
- advanced cache

### CacheConsole

```neon
console.cache:
	purge:
		- %tempDir%/cache
```

The `purge` parameter expects an array of dirs.

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

```neon
console.di:
	purge:
		- %tempDir%/cache/Nette.Configurator
```

The `purge` parameter expects an array of dirs.

Available commands:

- `nette:di:purge`

### LatteConsole

```neon
console.latte:
	warmup:
		- %appDir%
	warmupExclude: []
	purge:
		- %tempDir%/cache/latte
```

The `warmup`, `warmupExclude` and `purge` parameters are expecting an array of dirs.

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

This command supports:

- count parameter (`--count <count>` or `-c <count>` shortcut), to change the count of random strings. Default count is **10**.
- length parameter (`--length <length>` or `-l <length>` shortcut), to change the length of random strings. Default count is **50**.

### Database

```neon
console.database:
	backupPath: %appDir%/../backups/database
```

Backup database

`contributte:database:backup mysql 127.0.0.1 3306 username password database path/to file.sql`

- Path could be omitted (if defined in configuration)
- Filename could be omitted (will be generated)
- `--no-gzip` (`-g`) - disables gzip compression
- `--bin-path` (`-b`) - path to mysql/psql binary

Load database from backup

`contributte:database:load mysql 127.0.0.1 3306 username password database path/to/file.sql`

- `--bin-path` (`-b`) - path to mysql/psql binary

### AdvancedCacheConsole

#### Generator

Generate application cache with a single command

- `contributte:cache:generate`

  `--list` show list of available generators

  `--generator GENERATOR` use only specified generator

##### Register generators you want to use:

```neon
console.advancedCache:
	generators:
		latte: Contributte\Console\Extra\Cache\Generators\LatteTemplatesCacheGenerator(
			@Nette\Application\UI\ITemplateFactory,
			[%appDir%],
			[],
			::realpath(%appDir%/..)
		)
```

##### Available generators:

- Latte templates cache generator

```neon
Contributte\Console\Extra\Cache\Generators\LatteTemplatesCacheGenerator(
	[%appDir%],
	@Nette\Bridges\ApplicationLatte\ILatteFactory::create()
)
```

- DI containers generator

  - This example is configured to generate 3 containers - 1 for production mode, 1 for debug mode and 1 for console (should be enough for every application)
  - You don't need to add the `productionMode` parameter for Nette BC, it is done automatically.

```neon
Contributte\Console\Extra\Cache\Generators\DiContainersCacheGenerator(
	[
		debug: [debugMode: true, consoleMode: false],
		production: [debugMode: false, consoleMode: false],
		console: [debugMode: true, consoleMode: true]
	]
)
```

You will also need slightly modify `Bootstrap.php` and add Configurator as dynamic (imported) service to neon to get this generator work.

```php
$configurator->addServices(['configurator' => $configurator]);
```

```neon
services:
	configurator:
		type: Nette\Configurator
		imported: true
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
		return 'description which is shown in console when you run `contributte:cache:generate --list`';
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

Clean application cache with a single command

- `contributte:cache:clean`

  `--list` show list of available cleaners

  `--cleaner CLEANER` use only specified cleaner

##### Register cleaners you want to use:

```neon
console.advancedCache:
	cleaners:
		localFs: Contributte\Console\Extra\Cache\Cleaners\LocalFilesystemCleaner([%tempDir%])
```

##### Available cleaners:

- APC cleaner

```neon
Contributte\Console\Extra\Cache\Cleaners\ApcCleaner()
```

- APCu cleaner

```neon
Contributte\Console\Extra\Cache\Cleaners\ApcuCleaner()
```

- Local filesystem cleaner

```neon
Contributte\Console\Extra\Cache\Cleaners\LocalFilesystemCleaner([%tempDir%], [%tempDir%/ignored/])
```

- Memcache(d) cleaner

```neon
Contributte\Console\Extra\Cache\Cleaners\MemcachedCleaner([@memcache1, @memcache2])
```

- Nette\Caching\IStorage cleaner

```neon
Contributte\Console\Extra\Cache\Cleaners\NetteCachingStorageCleaner([@storage1, @storage2])
```

- Opcode cleaner

```neon
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
		return 'description which is shown in console when you run `contributte:cache:clean --list`';
	}

	public function clean(InputInterface $input, OutputInterface $output): bool
	{
		// clean cache
		// inform about it in console
		// return true if cleaning was successful, false otherwise
	}

}
```

## Compatibility

How to make this extension work with other Symfony/Console implementations.

### Kdyby/Console

`Kdyby` packages use the `kdyby.console.command` tag to mark its `Command` classes in order to find them. So it won't recognize commands from other packages which don't tag them this way.

This is where the decorator extension comes into play:

```neon
decorator:
	Symfony\Component\Console\Command\Command:
		tags: [kdyby.console.command]
```

Now `kdyby.console` will be able to recognize all available commands added by this extension.

## Examples

### 1. Example projects

We've made a few skeletons with preconfigured Contributte packages.

- https://github.com/contributte/webapp-skeleton
- https://github.com/contributte/apitte-skeleton

### 2. Example playground

- https://github.com/contributte/playground (playground)
- https://contributte.org/examples.html (more examples)

## Development

See [how to contribute](https://contributte.org) to this package. This package is currently maintained by these authors.

<a href="https://github.com/f3l1x">
    <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=80">
</a>

-----

Consider to [support](https://contributte.org/partners) **contributte** development team.
Also thank you for using this package.
