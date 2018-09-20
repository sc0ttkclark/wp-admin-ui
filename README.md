# WP Admin UI

A PHP class to build Admin interfaces within the WordPress Dashboard -- includes tables, add/edit forms, sorting, filtering, and exporting.

## Features

### Data sources

* Custom array of data
* SQL query that will get manipulated for pagination, sorting, and filtering

### Table lists

Similar to WP_List_Table but not an extension of it, WP Admin UI provides functionality that allows for:

* Creating a list table of data
* Sorting of any column (SQL mode only)
* Filtering of any column (SQL mode only)
* Pagination of data

### Exports

* CSV - Comma-separated Values (w/ Excel support)
* TSV - Tab-separated Values (w/ Excel support)
* TXT - Pipe-separated Values (w/ Excel support)
* XLSX - Excel format, using [PHP_XLSXWriter](https://github.com/mk-j/PHP_XLSXWriter)
* XML - XML 1.0 UTF-8 data
* JSON - JSON format
* PDF - PDF printer friendly views, using [TCPDF](https://tcpdf.org/)
* Custom - Custom delimiter separated Values (Update the report screen URL parameters to `&action=export&export_type=custom&export_delimiter=#` and change # to whatever delimiter you want)
