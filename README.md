# Hide Subpage Parents MediaWiki Extension

## About

This is an extension for [MediaWiki] that will hide the parents in the title of a subpage. For example, **Example/Sample/Data** will become **Data**.

## Installation

- Download this extension (grab the latest from [releases](https://github.com/bharley/mw-hidesubpageparents/releases))
- Extract this extension into `$mw/extensions/HideSubpageParents` where `$mp` is that path to your MediaWiki installation
- Add the following to `$mw/LocalSettings.php`:

```php
require_once "$IP/extensions/HideSubpageParents/HideSubpageParents.php";
```

## Usage

By default, all of your articles that are subpages will now only show the name of the subpage itself in the title. You can make this functionality optional by setting `$wgHideSubpageParentsDefaultOn` to `false`.

This extension also has two functions that can be used to modify the behavior of this extension on an article-by-article basis.

### `{{#showparents:[numberToShow]}}`
When called without arguments, this function will show all of the parents in the title of the subpage regardless of the `$wgHideSubpageParentsDefaultOn` setting.

When `numberToShow` is set, only that many parents will be shown. For example, **Example/Content/Data/2014/July** combined with `{{#showparents:3}}` will result in the page name **Content/Data/2014/July**.

### `{{#hideparents:[numberToHide]}}`
When called without arguments, this function will hide all of the parents in the title of the subpage regardless of the `$wgHideSubpageParentsDefaultOn` setting.

When `numberToHide` is set, only that many parents will be hidden. For example, **Example/Content/Data/2014/September** combined with `{{#hideparents:3}}` will result in the page name **2014/September**.

## Settings

There are a handful of settings available to configure this extension:

Setting                           | Since | Default | Description
--------------------------------- | ----- | ------- | -----------
`$wgHideSubpageParentsDefaultOn`  | 0.1   | `true`  | If this is set to false, subpages will not have their parent page titles removed by default.
`$wgHideSubpageParentsMainNSOnly` | 0.1   | `true`  | If this is set to false, parent page titles will be removed in every namespace.

Settings should go in your `LocalSettings.php` file **after** including the extension.

### Example

```php
// LocalSettings.php
// ...
require_once "$IP/extensions/HideSubpageParents/HideSubpageParents.php";

$wgHideSubpageParentsMainNSOnly = false;
// ...
```



[MediaWiki]: https://www.mediawiki.org

