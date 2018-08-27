# Neos Sentry Integration

Integrates the [Sentry error tracking tool](https://sentry.io/) into the Neos CMS.

## Installation

All you need to do to install this plugin, is to require it through composer in your projects root:

``` bash
composer require obisconcept/neos-sentry
```

## Configuration

Afterwards you need to configure at least the project key and identifier in your `Settings.yaml`:

``` yaml
ObisConcept:
  NeosSentry:
    project:
      key: 'MY PROJECT KEY'
      id: 'MY PROJECT ID'
```

You may find these information on your Sentry project page by clicking on "Installation Instructions" or through navigating to `https://sentry.io/[your-company]/[your-project]/getting-started/php`.
On this page, Sentry provides you a sample link for the Raven Client DSN, looking as following:

> https://3c613164xxxxxxxxxxx2beb58b3be87b@sentry.io/126xxxx

The first part before the `@`-sign is the key of your project.
The last part of the url after the slash is the id of your project.

### Self-hosted Sentry

If you use a self-hosted Sentry instance, you may also provide the `host` key in the configuration:

``` yaml
ObisConcept:
  NeosSentry:
    host: 'my-company.com'
```

### Turning off remote logging

If you want to turn off the automated logging functionalities (whysoever), you can simply disable it like this:

``` yaml
ObisConcept:
  NeosSentry:
    enabled: false
```

## Usage

_[To be written]_
<!-- There are no specific usage instructions needed for this plugin. -->

### Manual Logging

You can also make your class depend on the `ObisConcept\NeosSentry\Domain\Service\SentryClient` if you want to manually send messages or exceptions to Sentry.
This class exposes any method as mentioned in the [corresponding documentation](https://docs.sentry.io/clients/php/usage/).
