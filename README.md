# Neos Sentry Integration

Integrates the [Sentry error tracking tool](https://sentry.io/) into the Neos CMS.

## Installation

All you need to do to install this plugin, is to require it through composer in your projects root:

``` bash
composer require obisconcept/neos-sentry
```

## Configuration

For the Configuration of Sentry within Neos, you'll need at least two values.

- The project's public key
- The project's identifier

You may find these information on your Sentry project page by clicking on "Installation Instructions" or through navigating to `https://sentry.io/[your-company]/[your-project]/getting-started/php`.    
On this page, Sentry provides you a sample link for the Raven Client DSN, looking as following:

> https://3c613164xxxxxxxxxxx2beb58b3be87b@sentry.io/126xxxx

The first part before the `@`-sign is the key of your project.
The last part of the url after the slash is the id of your project.

### for automated error reporting

You need to set the Project configuration globally as an environmental variable.    
This is due to the fact, that you cannot rely on any component (such as the Configuration component from Flow) during error reporting, as it might be broken at this point in time.

It is recommended to configure the values in your local `.htaccess` file below the webroot.

_Example Config with Apache2:_

```
SetEnv SENTRY_HOST sentry.io
SetEnv SENTRY_PROJECT_KEY 3c613164xxxxxxxxxxx2beb58b3be87b
SetEnv SENTRY_PROJECT_ID 126xxxx
```

The host can be omitted as it defaults to `sentry.io`. Only specify it if you use a self-hosted Sentry instance.

If you omit one of the other variables, remote exception logging will _silently_ fail!    
This is because this plugin should never break any existing error reporting, and complaining about wrong configuration values within the handling of another error could lead to undefined behaviour.

### for manual usage

If you want to use the SentryClient service class directly in your code, you need to configure at least the project key and identifier in your `Settings.yaml`:

``` yaml
ObisConcept:
  NeosSentry:
    project:
      key: 'MY PROJECT KEY'
      id: 'MY PROJECT ID'
```

Unlike stated above, Sentry _will_ complain about configuration violations when using it manually.

If you use a self-hosted Sentry instance, you may also provide the `host` key in the configuration:

``` yaml
ObisConcept:
  NeosSentry:
    host: 'my-company.com'
```

## Usage

### Automated Logging

There are no special usage instructions required for the automated error reporting.
When you configured it properly as described above, it should work out-of-the-box.

**Please note** that the Sentry exception handler get's only registered automatically in Production context!    
If you want to have it working also in other contexts you have to configure it globally or per-context within your `Settings.yaml` as following:

``` yaml
Neos:
  Flow:
    error:
      exceptionHandler:
        className: 'ObisConcept\NeosSentry\Error\SentryExceptionHandler'
```

Also note, that the class `SentryExceptionHandler` extends the `Neos\Flow\Error\ProductionExceptionHandler` so no debugging output is shown directly on page!    
It is recommended to leave the handler configuration untouched and not to configure Sentry as handler for other contexts, because Neos rapidly floods up your quota with development related exception messages.

### Manual Logging

You can also make your class depend on the `ObisConcept\NeosSentry\Domain\Service\SentryClient` if you want to manually send messages or exceptions to Sentry.
This class exposes any method as mentioned in the [corresponding documentation](https://docs.sentry.io/clients/php/usage/).

## Data Privacy

**Please note** that Sentry collects a whole lot of data to provide you all informations you might need to fix the bug.
It might occur that it also stores individual-related data, what is subject to the regulations of the GDPR.
_It is your responsibility_ to inform your users about the usage of Sentry and to describe the data collected and the purpose for which it is used.
