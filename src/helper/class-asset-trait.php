<?php
/**
 * Assets Trait
 *
 * @package Ecomerciar\Pickit\Helper
 */

namespace Ecomerciar\Pickit\Helper;

trait AssetTrait {

	/**
	 * Gets Assets Folder URL
	 *
	 * @return string
	 */
	public static function get_assets_folder_url() {
		return plugin_dir_url( \PickitWoo::MAIN_FILE ) . 'assets';
	}

}
