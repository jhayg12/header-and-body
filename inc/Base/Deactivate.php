<?php
/**
 * @package  Header and Body Tag Injection Plugin
 */
namespace Inc\Base;

class Deactivate
{
	public static function deactivate() {
		flush_rewrite_rules();
	}
}