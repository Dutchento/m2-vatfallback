# Vatfallback for Magento 2

Vatfallback module provides an extra API based validation and a fallback offline regex validation 
for the unstable VIES database used by Magento

Please find the [Magento 1 version here](https://github.com/sandermangel/rkvatfallback/).

## Supported services

- Built in Magento VIES check
- Custom VIES check
- vatlayer.com check
- Regex fallback check for following countries; AT, BE, CZ, DE, CY, DK, EE, GR, ES, FI, FR, GB, HU, IE, IT, LT, LU, LV, MT, NL, PL, PT, SE, SI, SK

## How it works
A plugin wraps the existing Vat check in Magento Customer implementing various services as fallback

Or use the console task:
`./bin/magento vat:validate NL NL133001477B01`

## Compatibility

Tested on Mage Community & Commerce 2.2

## Magento 2
The Magento 2 version is currently under development here: [https://github.com/Dutchento/m2-vatfallback](https://github.com/Dutchento/m2-vatfallback)

## Changelog
[1.0.0] ported version of the Magento 1 module

## Requirements
- PHP 7
- GuzzleHTTP

## Disclaimer

Warning: Since all of the free VIES API's are slow and somewhat unreliable the checkout steps could become slow while checking.

## Authors

- Sander Mangel [@sandermagenl](https://twitter.com/sandermangel)

### Authors M1 Version

- Sander Mangel [@sandermagenl](https://twitter.com/sandermangel)
- Peter Jaap Blaakmeer [@peterjaap](https://twitter.com/peterjaap)
