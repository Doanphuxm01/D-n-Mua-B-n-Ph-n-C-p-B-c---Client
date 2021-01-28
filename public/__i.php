<?php
/**
 * Created by PhpStorm.
 * User: sakura
 * Date: 2/21/18
 * Time: 3:14 PM
 */
if (!is_writable(session_save_path())) {
    echo 'Session path "'.session_save_path().'" is not writable for PHP!';
}
echo phpinfo();