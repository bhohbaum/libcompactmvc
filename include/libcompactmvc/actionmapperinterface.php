<?php
if (file_exists('../libcompactmvc.php'))
	include_once ('../libcompactmvc.php');
LIBCOMPACTMVC_ENTRY;

/**
 * actionmapperinterface.php
 *
 * @author 		Botho Hohbaum <bhohbaum@googlemail.com>
 * @package		LibCompactMVC
 * @copyright   Copyright (c) Botho Hohbaum
 * @license 	BSD License (see LICENSE file in root directory)
 * @link		https://github.com/bhohbaum/LibCompactMVC
 */
interface ActionMapperInterface {

	/**
	 *
	 * @return String base URL
	 */
	public function get_base_url();

	/**
	 *
	 * @param String $path0
	 *        	path0 value
	 * @param String $path1
	 *        	path1 value
	 * @param String $urltail
	 *        	additional tail of URL
	 * @return String path of URL
	 */
	public function get_path($lang, $path0 = null, $path1 = null, $urltail = null);

}
