<?php
/**
 * Default options
 * 
 * PHP version 7
 *
 * @category  PHP
 * @package   STATS4WPPlugin
 * @author    Franck VANHOUCKE <ct4gg@ginkgos.net>
 * @copyright 2021-2023 Copyright 2023, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later
 * @version   1.4.0 GIT:https://github.com/thanatos-vf-2000/WordPress
 * @link      https://ginkgos.net
 */

return array(
    'install'                   => '0',
    'anonymize_ips'             => false,
    'ip_method'                 => 'REMOTE_ADDR',
    'addsearchwords'            => false,
    'store_ua'                  => true,
    'check_online'              => 120,
    'top_page'                  => 10,
    'disableadminstat'          => false,
    'geochart'                  => false,
);
