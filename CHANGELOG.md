# Search Filter Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 1.1.75 - 2020-08-17
- Added EVENT_ELEMENT_QUERY event for modifying element query on return.

## 1.1.7 - 2020-08-07
- Fixed category query deprecation warning.

## 1.1.6 - 2020-08-02
- Fixed js push state not working

## 1.1.5 - 2020-07-27
- Remove plain pagination on filter submission

## 1.1.4 - 2020-04-18
- Remove vue asset bundle call on variable setup method.

## 1.1.3 - 2020-04-12
- Fixed vue setup template clear filter with pre-defined is passed.

## 1.1.1 - 2020-04-07
- Changed pre-filter parameter format by accepting filter key
- Added attributes parameter option that will initialize loading of element attributes to optimize performance 
in Vue base template styles.

## 1.0.6.1 - 2020-03-17
- Changed set up option action url in the cp

## 1.0.6 - 2020-03-15
- Default category element search to and operator
- Passed pre-filter variables in the setup template.

## 1.0.5.1 - 2020-03-02
- Removed with variants to allow getVariants call to different siteIds.

## 1.0.5 - 2020-02-29
- Created item event hook that allows plugin customization to modify the search and item results from ajax.

## 1.0.4 - 2020-02-25
- Fixed issue cannot find included templates after super filter twig function is called by adding close twig function.

## 1.0.3 - 2020-02-25
- Added support for multi-site by filtering items based on the current site.

## 1.0.2.1 - 2020-02-23
- Fixed product element getting wrong sort options

## 1.0.2 - 2020-02-22
- Allows setup template to input pre defined filtered items to display, by adding `craft.superFilter.setup` 
second parameter to get pre filtered items on the item list display.

## 1.0.0 - 2020-02-19
### Added
- Initial release






