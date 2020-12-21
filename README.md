# Move Disapproved Posts extension for phpBB

Moves disapproved posts to another forum.

[![Build Status](https://travis-ci.com/david63/movedisapproved.svg?branch=master)](https://travis-ci.com/david63/movedisapproved)
[![License](https://poser.pugx.org/david63/movedisapproved/license)](https://packagist.org/packages/david63/movedisapproved)
[![Latest Stable Version](https://poser.pugx.org/david63/movedisapproved/v/stable)](https://packagist.org/packages/david63/movedisapproved)
[![Latest Unstable Version](https://poser.pugx.org/david63/movedisapproved/v/unstable)](https://packagist.org/packages/david63/movedisapproved)
[![Total Downloads](https://poser.pugx.org/david63/movedisapproved/downloads)](https://packagist.org/packages/david63/movedisapproved)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/59902be2665c476dbd7951858c9ff769)](https://www.codacy.com/manual/david63/movedisapproved?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=david63/movedisapproved&amp;utm_campaign=Badge_Grade)

## Minimum Requirements
* phpBB 3.3.0
* PHP 7.1.3

## Install
1. [Download the latest release](https://github.com/david63/movedisapproved/archive/3.3.zip) and unzip it.
2. Unzip the downloaded release and copy it to the `ext` directory of your phpBB board.
3. Navigate in the ACP to `Customise -> Manage extensions`.
4. Look for `Move disapproved posts` under the Disabled Extensions list and click its `Enable` link.

## Usage
1. Create a forum into which the topics/posts are to be moved.
2. Apply permissions to the forum just created to restict viewing to Administratots and/or Moderators.
3. Apply the permissions to the forum just created that do not increase post count.
4. Navigate in the ACP to `Extensions -> Move disapproved posts -> Manage move disapproved`.
5. Apply the settings that you require.

## Uninstall
1. Navigate in the ACP to `Customise -> Manage extensions`.
2. Click the `Disable` link for `Move disapproved posts`.
3. To permanently uninstall, click `Delete Data`, then delete the movedisapproved folder from `phpBB/ext/david63/`.

## License
[GNU General Public License v2](http://opensource.org/licenses/GPL-2.0)

© 2020 - David Wood