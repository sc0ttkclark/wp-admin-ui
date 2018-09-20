# WP Admin UI

A PHP class to build Admin interfaces within the WordPress Dashboard -- includes tables, add/edit forms, sorting, filtering, and exporting.

## Features

There are actions and filters all over to enable extending functionality.

### Data sources

* Custom array of data
* SQL query that will get manipulated for pagination, sorting, and filtering

### Table lists

This functionality is ssimilar to WP_List_Table but not an extension of it.

* Creating a list table of data
* Sorting of any column (SQL mode only)
* Filtering of any column (SQL mode only)
* Pagination of data
* Delete items in table
* Reorder items in table
* Custom actions

### Add / Edit / Duplicate / View screens

This functionality is similar to the post editor but not an extension of it.

* Add new item
* Edit item
* Duplicate existing item with add new form
* View item
* Custom actions

### Exports

* CSV - Comma-separated Values (w/ Excel support)
* TSV - Tab-separated Values (w/ Excel support)
* TXT - Pipe-separated Values (w/ Excel support)
* XLSX - Excel format, using [PHP_XLSXWriter](https://github.com/mk-j/PHP_XLSXWriter)
* XML - XML 1.0 UTF-8 data
* JSON - JSON format
* PDF - PDF printer friendly views, using [TCPDF](https://tcpdf.org/)
* Custom - Custom delimiter separated Values (Update the report screen URL parameters to `&action=export&export_type=custom&export_delimiter=#` and change # to whatever delimiter you want)

### Column type support

* Text
* Date
* Time
* Date + Time
* Related (via table of data)
* Boolean (checkbox / yes+no)
* Number (1,234
* Decimal (234.99)
