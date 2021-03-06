/*************************************************************************************************
 * ormclient.js
 *
 * This file is automatically generated by the framework to provide easy access to all DTOs.
 *
 * @author Botho Hohbaum <bhohbaum@gmail.com>
 * @package LibCompactMVC
 * @copyright Copyright (c) Botho Hohbaum
 * @license BSD License (see LICENSE file in root directory)
 * @link https://github.com/bhohbaum/LibCompactMVC
 *************************************************************************************************/

<?php foreach ($this->get_value("tables") as $table) { ?>
//************************************************************************************************
// <?= $table ?> 
//************************************************************************************************
// constructor
var <?= $table ?> = function() {
	Ajax.DbObject.call(this, '<?= $this->get_value("endpoint_" . $table) ?>');
	this.__type = "<?= $table ?>";
};

// set prototype (derive from Ajax.DbObject)
<?= $table ?>.prototype = Object.create(Ajax.DbObject.prototype);

// fix constructor
<?= $table ?>.prototype.constructor = <?= $table ?>;

// <?= (count($this->get_value("methods_" . $table)) > 0) ? "" : "no " ?>methods
<?php foreach ($this->get_value("methods_" . $table) as $method) { ?>
<?php if ($this->get_value("method_" . $table. "::" . $method)) { ?>
<?= $table ?>.prototype.<?= $method ?> = function(cb, param) {
	this.callMethod(cb, "<?= $method ?>", param);
}
<?php } else { ?>
<?= $table ?>.prototype.<?= $method ?> = function(cb) {
	this.callMethod(cb, "<?= $method ?>");
}
<?php } ?>

<?php } ?>


<?php } ?>
