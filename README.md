
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

**IMPORTANT NOTE**

Deployment steps shouldn't be followed until after the PR containing your changes has been approved and merged. Release/Deploy PRs shouldn't contain any function changes.

1. Create a PR for your changes (bug fix, update, etc).
2. Get a review and approval.
3. Merge your change PR.
4. Start following the automated deployment process below.

### Automated Deployment

We now have a series of scripts that will help with automating the release process. To use these scripts, do the following.

1. Make sure you're running NodeJS v16. If you use [nvm](https://github.com/nvm-sh/nvm), you can simply type `nvm use`. If you prefer [fnm](https://github.com/Schniz/fnm), you can run `fnm use` instead.
2. Run `npm install` in the project root to make sure all dependencies are installed at the project level and in the `./scripts` folder.
3. Run `composer install` in the project root to make sure you have the binaries for prepare the release and prevent errors like `vendor/bin/phpcs-changed: No such file or directory`.
4. Run `npm run prepare-release` in the project root. This will guide you through incrementing the version, updating the changelog, and creating a PR.
5. Once that PR is approved and merged, run `npm run create-release`. Follow the prompts to do a build, create a new release tag, and deploy the release tag.

_NOTE: Creating a new release doesn't automatically deploy the new version. A corresponding update PR will need to be opened to update the wc-calypso-bridge dependency in wpcomsh._

[Here's an example](https://github.com/Automattic/jetpack/pull/43575) of wpcomsh PR to create after finishing the release. Tip: once you're inside the [wpcomsh folder](https://github.com/Automattic/jetpack/tree/trunk/projects/plugins/wpcomsh), use `composer require automattic/wc-calypso-bridge:x.yy.z` to update both `composer.json` and `composer.lock` files to the new version.

### Manual Deployment

If, for some reason, you are unable to use the automated build/release scripts above, the process can be done manually as described below.

1. Create a [version bump pull request](https://github.com/Automattic/wc-calypso-bridge/pull/613/files) on `wc-calypso-bridge` that increments the [version](https://github.com/Automattic/wc-calypso-bridge/blob/master/composer.json#L3) in `composer.json`, and in [two spots](https://github.com/Automattic/wc-calypso-bridge/blob/master/wc-calypso-bridge.php#L33) in `wc-calypso-bridge.php`.
2. [Add a section](https://github.com/Automattic/wc-calypso-bridge/blob/master/readme.txt#L23-L26) to the `readme.txt` also with a changelog of what is in the release.
3. Mark the pull request as ready for review, and **merge** once a 👍 review is given.
4. Make sure you've closed your `start` script if you have it running.
5. *Back in your local copy of wc-calypso-bridge*, perform the following steps:
    1. `git checkout master`
    2. `git pull origin master`
    3. `nvm use` in case your current node version is different
    4. `npm i`
    5. `npm run build`
    6. `rm -rf node_modules` so these don't get packaged up in the release

6. Now you are ready to tag the release by replacing x.x.x with the version number being created:
    1. `git tag x.x.x`
    2. `git checkout x.x.x`
    3. `git add ./build --force`
    4. `git commit -m 'Adding build directory to release' --no-verify`
    5. `git tag -f x.x.x`
    6. `git push origin x.x.x`
7. 🎊 Congrats, you have released a new version of `wc-calypso-bridge`.

_NOTE: Creating a new release doesn't automatically deploy the new version. A corresponding update PR will need to be opened to update the wc-calypso-bridge dependency in wpcomsh._

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
| `wc_calypso_bridge_has_ecommerce_features()`   | This will return `true` for all ecommerce-related plans *(including all WordPress.com, Woo Express plans, and the Ecommerce Free Trial)* |
| `wc_calypso_bridge_is_ecommerce_plan()`   | Returns `true` if the site has a paid Ecommerce plan (including all WordPress.com and Woo Express plans) |
| `wc_calypso_bridge_is_woo_express_trial_plan()`  |  Returns `true` if the site has a Woo Express Trial plan. |
| `wc_calypso_bridge_is_woo_express_performance_plan()`  |  Returns `true` if the site has a Woo Express Performance plan. |
| `wc_calypso_bridge_is_woo_express_essential_plan()`  |  Returns `true` if the site has a Woo Express Essential plan. |
| `wc_calypso_bridge_is_wpcom_ecommerce_plan()`  |  Returns `true` if the site has an Ecommerce plan from WordPress.com. |
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
