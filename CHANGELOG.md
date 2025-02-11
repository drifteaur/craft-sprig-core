# Release Notes for Sprig Core

## 3.5.1 - 2024-09-11

### Fixed

- Fixed the ability to pass a JSON encoded string into the `sprig.triggerEvents()` function ([#15](https://github.com/putyourlightson/craft-sprig-core/issues/15).

## 3.5.0 - 2024-08-29

### Added

- Added the ability to assign an ID to a component by passing a string as the third argument to the `sprig()` function.

### Changed

- Updated htmx to version 2.0.2 ([changelog](https://github.com/bigskysoftware/htmx/blob/master/CHANGELOG.md#202---2024-08-12)).

### Fixed

- Fixed the positioning of the JavaScript output by the `sprig.triggerRefreshOnLoad` function.

### Deprecated

- Deprecated `sprig.getIsBoosted`, `sprig.getIsError`, `sprig.getIsHistoryRestoreRequest`, `sprig.getIsInclude`, `sprig.getIsRequest`, `sprig.getIsSuccess`. Use `sprig.isBoosted`, `sprig.isError`, `sprig.isHistoryRestoreRequest`, `sprig.isInclude`, `sprig.isRequest`, `sprig.isSuccess` instead.

## 3.4.1 - 2024-08-20

### Fixed

- Fixed a bug that was breaking the `Sprig` variable in the browser console with `devMode` enabled.

## 3.4.0 - 2024-08-20

### Changed

- The `sprig.registerJs(js)` function now executes the registered JavaScript after htmx settles, and is now the recommended way of outputting JavaScript in Sprig components.
- Components no longer render markup added via `{% html %}`, `{% css %}` and `{% js %}` tags during Sprig requests.

## 3.3.2 - 2024-08-12

### Fixed

- Fixed a bug in which asset bundles were being registered and rendered in control panel Sprig requests ([#383](https://github.com/putyourlightson/craft-sprig/issues/383)).

## 3.3.1 - 2024-08-08

### Fixed

- Fixed CSRF tokens not being sent with requests using a method other than `GET`.

## 3.3.0 - 2024-08-08

### Added

- Added the component configurations to a `Sprig` variable in the browser console when `devMode` is enabled.
- Added the ability to pass any value into `s-method`, including the `put`, `patch` and `delete` verbs.
- Added the `sprig.registerJs(js)` function.

### Changed

- The `sprig.triggerRefresh(selector, variables)` function now prevents cyclical requests.

## 3.2.1 - 2024-08-06

### Changed

- The `sprig.swapOob()` function now accepts a string, in addition to a template to be rendered, in the second argument.

## 3.2.0 - 2024-08-01

### Added

- Added the `sprig.swapOob(selector, template, variables)` function that swaps a template out-of-band.
- Added the `sprig.triggerRefresh(selector, variables)` function that triggers a refresh on a Sprig component.

### Changed

- Components now render markup added via `{% html %}`, `{% css %}` and `{% js %}` tags.
- The `sprig.triggerRefreshOnLoad(selector)` function now appends output to the end of the body and should be called using `{% do sprig.triggerRefreshOnLoad(selector) %}`.

## 3.1.0 - 2024-07-15

> [!IMPORTANT]
> This update introduces htmx 2. [Read about the changes →](https://putyourlightson.com/articles/sprig-htmx-2)

### Added

- Added the [s-inherit](https://putyourlightson.com/plugins/sprig#s-inherit) attribute that allows you to control and enable automatic attribute inheritance for child nodes, if it has been disabled by default.

### Changed

- Updated htmx to version 2.0.1 ([2.0.0 release notes](https://htmx.org/posts/2024-06-17-htmx-2-0-0-is-released/)).
- The htmx file is now output even when Sprig components are used inside of `{% cache %}` tags ([#329](https://github.com/putyourlightson/craft-sprig/issues/329)).

### Removed

- Removed the `s-sse` and `s-ws` attributes.

## 3.0.4 - 2024-05-22

### Fixed

- Fixed a bug in which variables passed into a component were being converted to strings ([#369](https://github.com/putyourlightson/craft-sprig-core/issues/369)).

## 3.0.3 - 2024-05-15

### Changed

- Made it possible to pass a fully namespaced component class into the `sprig()` function ([#14](https://github.com/putyourlightson/craft-sprig-core/issues/14)).

## 3.0.2 - 2024-04-26

### Changed

- Updated htmx to version 1.9.12 ([changelog](https://github.com/bigskysoftware/htmx/blob/master/CHANGELOG.md#1912---2024-04-17)).

## 3.0.1 - 2024-04-21

### Fixed

- Fixed a bug in which some `sprig` variables were incorrectly persisting across requests ([#363](https://github.com/putyourlightson/craft-sprig/issues/363)).

## 3.0.0 - 2024-04-08

> {warning} Template caches and static page caches should be cleared after performing this update.

### Added

- Added compatibility with Craft 5.

### Changed

- Changed how the component configuration is encoded.

### Removed

- Removed the `sprig.script` variable.
- Removed the `s-on` attribute.
- Removed the `success` variable. Use `sprig.isSuccess` or `sprig.isError` instead.
- Removed the `flashes` variable. Use `sprig.message` instead.
- Removed the `id` variable. Use `sprig.modelId` instead.
