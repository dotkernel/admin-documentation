# NPM Commands

## Summary

This page lists the NPM commands used to install front-end dependencies, watch assets for changes, and build production-ready assets.

## Details

To install dependencies into the `node_modules` directory run this command.

```shell
npm install
```

> If `npm install` fails, this could be caused by user permissions of npm.
> The recommended way to install npm is through `Node Version Manager`.

The watch command compiles the components, then monitors the files for changes and recompiles them.

```shell
npm run watch
```  

After all updates are done, this command compiles the assets locally, minifies them and makes them ready for production.

```shell
npm run prod
```

## FAQ

**Q: How do I install front-end dependencies?**

A: Run `npm install` to install dependencies into the `node_modules` directory.

**Q: What should I do if `npm install` fails?**

A: This is usually caused by npm user permissions; the recommended fix is to install npm through `Node Version Manager`.

**Q: How do I get assets to recompile automatically while developing?**

A: Run `npm run watch`, which compiles the components and then monitors files for changes, recompiling them as needed.

**Q: How do I prepare assets for production?**

A: Run `npm run prod` to compile and minify the assets locally, making them ready for production.
