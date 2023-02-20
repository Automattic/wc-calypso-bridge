
# WooCommerce Calypso Bridge

This repository houses various fixes and extensions for wp-admin to enhance the experience for users of the WordPress.com Store.

## Pre-Flight Checklist

Before activating `master` in your WordPress environment, remember to install dependencies and build all the assets:
```
npm install
npm run build
```
**Note:** Ensure that you currently running the [required](https://github.com/Automattic/wc-calypso-bridge/blob/master/.nvmrc) NodeJS version. Check [NVM](https://www.npmjs.com/package/nvm) for running multiple node versions.

## Deployment

1. Create a [version bump pull request](https://github.com/Automattic/wc-calypso-bridge/pull/613/files) on `wc-calypso-bridge` that increments the [version](https://github.com/Automattic/wc-calypso-bridge/blob/master/composer.json#L3) in `composer.json`, and in [two spots](https://github.com/Automattic/wc-calypso-bridge/blob/master/wc-calypso-bridge.php#L33) in `wc-calypso-bridge.php`.
2. [Add a section](https://github.com/Automattic/wc-calypso-bridge/blob/master/readme.txt#L23-L26) to the `readme.txt` also with a changelog of what is in the release.
3. Mark the pull request as ready for review, and **merge** once a 👍 review is given.
4. Make sure you've closed your `start` script if you have it running.
5. *Back in your local copy of wc-calypso-bridge*, perform the following steps:
  1. `git checkout master`
  2. `git pull origin master`
   3. `npm i`
   4. `npm run build`
   5. `rm -rf node_modules` so these don't get packaged up in the release

6. Now you are ready to tag the release by replacing x.x.x with the version number being created:
   1. `git tag x.x.x`
   2. `git checkout x.x.x`
   3. `git add ./build --force`
   4. `git commit -m 'Adding build directory to release' --no-verify`
   5. `git tag -f x.x.x`
   6. `git push origin x.x.x`
7. 🎊 Congrats, you have released a new version of `wc-calypso-bridge`.

## Development Environment

To get started with development of front-end components run `npm start`.

See [wp-scripts](https://github.com/WordPress/gutenberg/tree/master/packages/scripts) for more usage information.

#### Running under a local WordPress installation
To use this plugin, several conditions must be met.

##### Plugin Dependencies

- WooCommerce >= 7.3.0
- Jetpack

Note that the plugin expects to find these plugins in their original folders, so renaming them may prevent the plugin from running.

##### Jetpack Connection & Option Values

You will need either a connected Jetpack site or use the following filter to force Jetpack into development mode:

`add_filter( 'jetpack_offline_mode', '__return_true' );`

For the Bridge to work, the site must have a plan. [Download this gist](https://gist.github.com/moon0326/cac46c70a2cee81b61faef517fef7178) (recommended) as a plugin and activate it.

#### Running on WoA

The recommended way for developing on Atomic is to have a `rsync` configuration in place for sending files directly into your WoA.


## Contributing to The Bridge

### Repository Structure

Key folders and files:

- The `src` folder includes JS/TSX files compiled with Webpack,
- The `includes` folder contains PHP files,
- The `class-wc-calypso-bridge-shared.php` file contains logic for enqueueing assets and managing localized parameters.
- The `class-wc-calypso-bridge-dotcom-features.php` file includes logic for determing the active plan. More information below.

Before contributing, it's recommended to scan the `includes` folder in case the fix can be placed in existing controllers/files. If not, create a new file, e.g., `includes/class-wc-calypso-bridge-my-custom-fix.php`, and ensure that it gets included in the [includes](https://github.com/Automattic/wc-calypso-bridge/blob/master/class-wc-calypso-bridge.php#L100-L122) section of the main controller.

Regarding the timeline, all files under the `includes` folder will be loaded at `plugins_loaded` at `0` priority.

Here's a handy boilerplate for starting a new file in [this gist](https://gist.github.com/somewherewarm-snippets/ee3d68b9bfb56232fdd94a2edbcfd25e).

### Active Plan Detection

Plan-specific tweaks can be managed using the following global functions:
| Helper function |  Return value  |
|---|---|
| `wc_calypso_bridge_has_ecommerce_features()`   | This will return `true` for all ecommerce-related plans *(such as the Ecommerce and Ecommerce Trial.)* |
| `wc_calypso_bridge_is_ecommerce_plan()`   | Returns `true` if site has the specific Ecommerce plan. e.g., `false` for the trial plan. |
| `wc_calypso_bridge_is_ecommerce_trial_plan()`  |  Returns `true` if the site has an Ecommerce Trial plan. |
| `wc_calypso_bridge_is_business_plan()` | Returns `true` if the site has a business plan. |

Similarly, on the JS side, use the global `window.wcCalypsoBridge` object for fetching information about the active plan:
- `window.wcCalypsoBridge.hasEcommerceFeatures (bool)`
- `window.wcCalypsoBridge.isEcommercePlan (bool)`
- `window.wcCalypsoBridge.isEcommerceTrialPlan (bool)`

**Note:** This list will be updated as new plans come into play.

### Test Suite

This repo runs a CI process during PRs using GH Actions workflows. For now, we only run the following:
- PHPCS checks to ensure some basic coding standards,
- A file comparison check in the build files.

More to come in this section.
