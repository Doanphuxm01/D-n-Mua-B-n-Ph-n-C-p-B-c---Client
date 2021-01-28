<?php
/**
 * Created by PhpStorm.
 * User: Sakura
 * Date: 7/20/18
 * Time: 9:59 PM
 */

/**
 * git fetch --all
git reset --hard origin/master
 */
echo "DONE! ok rồi!";
$output = shell_exec('/usr/bin/git 2>&1');
$output = shell_exec('/usr/bin/git 2>&1');
echo "<pre>$output</pre>";