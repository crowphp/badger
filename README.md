# badger
Badger is a small CLI utility for generating a shields.io compatible JSON from clover.xml report and uploading it to a badger-server, see github.com/crowphp/badger-server to read more about the server.

## Installing badger from composer

```
composer require --dev crowphp/badger:dev-master
```

Notice: At the moment the badger is under development use it with caution.

## Usage

Help Document

```
$ php vendor/bin/badger --help
Console Tool

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display help for the given command. When no command is given display help for the list command
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  help             Displays help for a command
  list             Lists commands
 upload
  upload:coverage  Creates json for coverage badge shields.io and uploads it to a given badger server

```

Create badge schema on a badger server for a given branch
```
$ php upload:coverage <server> <branch> <secret-key>

Description:
  Creates json for coverage badge shields.io and uploads it to a given badger server

Usage:
  upload:coverage <server> <branch> <secret-key>

Arguments:
  server                URL for Badger Server
  branch                The branch name for the coverage
  secret-key            Secret api key for badger server

Options:
  -h, --help            Display help for the given command. When no command is given display help for the list command
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

```

