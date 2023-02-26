# Audit Log Plugin

[![Build Status](https://travis-ci.org/hevertonfreitas/cakephp-audit-log.svg?branch=master)](https://travis-ci.org/hevertonfreitas/cakephp-audit-log)
[![Latest Stable Version](https://poser.pugx.org/hevertonfreitas/cakephp-audit-log/v/stable)](https://packagist.org/packages/hevertonfreitas/cakephp-audit-log)
[![Total Downloads](https://poser.pugx.org/hevertonfreitas/cakephp-audit-log/downloads)](https://packagist.org/packages/hevertonfreitas/cakephp-audit-log)
[![Latest Unstable Version](https://poser.pugx.org/hevertonfreitas/cakephp-audit-log/v/unstable)](https://packagist.org/packages/hevertonfreitas/cakephp-audit-log)
[![codecov](https://codecov.io/gh/hevertonfreitas/cakephp-audit-log/branch/master/graph/badge.svg)](https://codecov.io/gh/hevertonfreitas/cakephp-audit-log)
[![License](https://poser.pugx.org/hevertonfreitas/cakephp-audit-log/license)](http://opensource.org/licenses/MIT)

A logging plugin for [CakePHP](https://cakephp.org). The included `AuditableBehavior`  creates an audit history for each instance of a model to which it's attached.

The behavior tracks changes on two levels. It takes a snapshot of the fully hydrated object _after_ a change is complete and it also records each individual change in the case of an update action.

## Features

* Support for CakePHP 4.0
* Tracks object snapshots as well as individual property changes.
* Allows each revision record to be attached to a source -- usually a user -- of responsibility for the change.
* Allows developers to ignore changes to specified properties. Properties named `created`, `updated` and `modified` are ignored by default, but these values can be overwritten.
* Handles changes to HABTM associations.
* Does not require or rely on the existence of explicit models revisions (`AuditLog`) and deltas (`AuditLogDeltas`).

## Installation

You can install this plugin into your CakePHP application using [composer](https://getcomposer.org).

The recommended way to install composer packages is:

```
composer require hevertonfreitas/cakephp-audit-log
```

Load your plugin using
```
bin/cake plugin load AuditLog
```
or by manually putting `$this->addPlugin('AuditLog');` in the bootstrap method of your `src/Application.php`.

Then, run the necessary migrations
```
bin/cake migrations migrate -p AuditLog
```

### Next Steps

1. Create a `currentUser()` method, if desired.

    The `AuditableBehavior` optionally allows each changeset to be "owned" by a "source" -- typically the user responsible for the change. Since user and authentication models vary widely, the behavior supports a callback method that should return the value to be stored as the source of the change, if any.

    The `currentUser()` method must be available to every model that cares to track a source of changes. It should return an array with a key `id` describing the primary key of the user that changed the record. 

## Usage

Applying the `AuditableBehavior` to a model is essentially the same as applying any other CakePHP behavior. The behavior does offer a few configuration options:

<dl>
	<dt>`ignore`</dt>
	<dd>An array of property names to be ignored when records are created in the deltas table.</dd>
	<dt>`habtm`</dt>
	<dd>An array of models that have a HABTM relationship with the acting model and whose changes should be monitored with the model. If the HABTM model is auditable in its own right, don't include it here. This option is for related models whose changes are _only_ tracked relative to the acting model.</dd>
</dl>

## License

This code is licensed under the [MIT license](http://www.opensource.org/licenses/mit-license.php).

## Notes

Feel free to submit bug reports or suggest improvements in a ticket or fork this project and improve upon it yourself. Contributions welcome.
