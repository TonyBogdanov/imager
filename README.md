# Imager

[![Buy Me a Coffee](http://static.tonybogdanov.com/github/coffee.svg)](http://ko-fi.co/1236KUKJNC96B)

Console tool for batch generation of images using [ImageMagick](http://www.imagemagick.org/script/index.php).

## Disclaimer

For now, this is a "private" project, e.g. it is very specific to my needs and is not targeted towards general use. You can
still do whatever you want with it if you find it useful.

## Requirements

- PHP 5.5 or later with the `php` command globally available in the console.
- [ImageMagick](http://www.imagemagick.org/script/index.php) installed and properly configured.
- The `convert` command globally available in the console.

## Installing

### Using [NPM](http://npmjs.org)

```shell
npm install --save TonyBogdanov/imager
```

### Using [Bower](http://bower.io/)

```shell
bower install --save TonyBogdanov/imager
```

### Manually

To install the tool manually simply download `build/imager.phar`.

## Usage

Open your favourite console and run the following:

```shell
php imager.phar init
```

This will start an interactive wizard which will help you create a `imager.json` file with information on
what operations to perform.

Once you have the file you'll be able to call:

```shell
php imager.phar generate path/to/imager.json
```

or simply

```shell
php imager.phar generate
```

from the same directory.

Keep in mind that any relative paths you set in the `imager.json` file will be relative to the directory from which
you run the `generate` command, NOT to the directory where the `imager.json` file is located.

As a recommendation you should always run the commands in the directory of `imager.json` or use absolute paths.

## Running as a NodeJS script

Say you've installed Imager to your project as a NPM dependency. Now you can simply add:

```json
"image": "cd build/imager & imager generate -vv"
```

to your `package.json` file to automate the image generation even further.

Now you will have a NPM script called `image`, which you can run via:

```shell
npm run image
```

When run, the console will `cd` to the `build/imager` directory and execute the `imager generate -vv` command.
In this example we assume you have a `imager.json` file in `build/imager`.

As far as `imager` is concerned, `build/imager` will also be the current working directory.

The path we `cd` into must be relative to where your `package.json` file is located. NodeJS makes it easier by always
running from the directory of your `package.json`, which means you can run `npm run image` even from a sub-directory.