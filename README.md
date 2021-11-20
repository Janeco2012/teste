# Images.weserv.nl

[![Author](https://img.shields.io/badge/author-andrieslouw-blue.svg)](https://github.com/andrieslouw)
[![Author](https://img.shields.io/badge/author-kleisauke-blue.svg)](https://github.com/kleisauke)
[![Source Code](https://img.shields.io/badge/source-weserv/images-blue.svg)](https://github.com/weserv/images)
[![Software License](https://img.shields.io/badge/license-BSD3-brightgreen.svg)](https://opensource.org/licenses/BSD-3-Clause)
[![Build Status](https://travis-ci.org/weserv/images.svg?branch=4.x)](https://travis-ci.org/weserv/images)
[![Coverage Status](https://coveralls.io/repos/github/weserv/images/badge.svg?branch=4.x)](https://coveralls.io/github/weserv/images?branch=4.x)

Source code of images.weserv.nl, to be used on your own server(s). Images.weserv.nl leverages powerful libraries like [lua-vips](https://github.com/libvips/lua-vips) (for image handling and manipulation) and [lua-resty-http](https://github.com/pintsized/lua-resty-http) (for sending HTTP requests).

## Technologies used

- Linux, [nginx](https://github.com/nginx/nginx), [libvips](https://github.com/libvips/libvips) and [OpenResty](https://github.com/openresty/) (without these technologies, this project would never have been possible)
- [Cloudflare](https://www.cloudflare.com/) (for caching and IP-blocking)
- [Redis](https://github.com/antirez/redis) (for rate limiting)
- [OpenDNS](https://www.opendns.com/) (for DNS-filtering)

## Documentation

See our [wiki documentation](https://github.com/weserv/images/wiki) or [API reference](https://images.weserv.nl/) for information on using images.weserv.nl.

## Docker deployment

For information on Docker deployment, please read the [Docker installation instructions](DOCKER.md).

## Submitting Bugs and Suggestions

We track support tickets, issues and feature requests using the [GitHub issue tracker](https://github.com/weserv/images/issues).

## Credits
[![Andries Louw Wolthuizen](https://avatars2.githubusercontent.com/u/11487455?v=3&s=120)](https://github.com/andrieslouw) | [![Kleis Auke Wolthuizen](https://avatars2.githubusercontent.com/u/12746591?v=3&s=120)](https://github.com/kleisauke)
------------- | -------------
[Andries Louw Wolthuizen](https://github.com/andrieslouw) | [Kleis Auke Wolthuizen](https://github.com/kleisauke)

## License

The BSD 3-Clause License. Please see [LICENSE.md](LICENSE.md) for more information.

## Privacy Policy

Please see [Privacy-Policy.md](Privacy-Policy.md) for more information.

## Last but not least
This is made in Sneek with love and passion

[![Made in Sneek](https://kleisauke.nl/made-in-sneek-resized.png)](https://en.wikipedia.org/wiki/Sneek)
