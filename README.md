LibCompactMVC
=============

LibCompactMVC is a lightweight PHP framework including:

- ORM
- Pages / Controller Routing
- HTMLMail
- Logging
- Cached Templating
- Request Based Response Cache
- Session Handling
- ... and a lot of different useful functionalities.

It can also be used for PHP based REST APIs.

Installation
============

- Install the project to a webserver with the project root folder beeing the
  document root.
- Open man/demodb.mwb with the MySQL Workbench and forward engineer the
  database.
- Apply man/dbscripts/event_types.sql and man/dbscripts/mailpart_types.sql to
  the DB.
- Make all required changes to include/config.php.
- Have fun!


Controller
==========

Place the controller files under /application/controller/. Every controller
must be derived from CMVCController.

Methods to overwrite
--------------------

There are several methods that can be overwritten. It depends on the HTTP verb
whitch ones are executed. These are their definitions:

- protected function retrieve_data_get();
- protected function retrieve_data_post();
- protected function retrieve_data_put();
- protected function retrieve_data_delete();
- protected function retrieve_data();
- protected function run_page_logic_get();
- protected function run_page_logic_post();
- protected function run_page_logic_put();
- protected function run_page_logic_delete();
- protected function run_page_logic();
- protected function exception_handler($e);

For examplte, when a GET request occurs, the following methods are called in
order:

- protected function retrieve_data_get():
- protected function retrieve_data();
- protected function run_page_logic_get();
- protected function run_page_logic();

In case an exception occurs in one of the upper methods, exception_handler($e)
is called, where it is up to you to re-throw the exception or to handle it in
some other way.

In case you do not want to use the default database access object in a specific
controller, overwrite:

- protected function dba();

and return the name of the DBA class as string. This will change the object
referenced by $this->db to an instance of the corrensponding class.
For further details see section "ORM".

Methods to call
---------------







Routing
=======




HTMLMail
========




ORM
===






