<?php
/**
 * @package  Header and Body Tag Injection Plugin
 */
namespace Inc\Base;

class Activate
{
	public static function activate() {
		flush_rewrite_rules();
	}
}