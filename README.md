SphinxAutocompleteExample
=========================

These samples illustrate autocomplete and type suggestion using Manticore/Sphinx search.     
Querying is made via SphinxQL using PDO driver.    
The sample data included contains a version of Sphinx documentation.  
The samples are featured on [this Sphinx blog post] (http://sphinxsearch.com/blog/2013/05/21/simple-autocomplete-and-correction-suggestion/).    

Requirements :
-------------------------------------------
LAMP  
Manticore or Sphinx search  
PHP with PDO mysql  

Installation :
-------------------------------------------
Edit `scripts/sphinx.conf` for setting proper paths and db credentials
Import the sample tables in your database.    
Untar first the two archieves in `scripts` folder:
    
    $ tar -xzvf suggest.tar.gz 
    $ tar -xzvf docs.tar.gz
    $ mysql < suggest.sql
    $ mysql < docs.sql
Alternative you can build the suggest table by following instructions in `scripts/suggest/README`

The samples use 3 indexes : `simplecompletefull` for doing the actual search, `simplecomplete` for autocomplete  and `suggest` for suggestions.   
`simpletecompletefull` can be used for autocomplete as well, if the search for completion is made only on title ( `@title $query` ).   
Index the 3 indexes:
 
    $ indexer -c /path/to/sphinx.conf --all
    
Start a new Manticore/Sphinx server using sphinx.conf from `scripts` folder or import the indexes if you already have a running Sphinx server. 
 
    $ searchd -c /path/to/sphinx.conf
In case you start a new Manticore/Sphinx server, be sure to change the ports in **sphinx.conf** and **common.php**.
In **common.php** edit the database credentials. For Sphinx 2.1.1 or greater/Manticore or trunk version, set constant `SPHINX_20` to **false**.         
Autocomplete starts after typing 3 characters. To change this you need to edit in **sphinx.conf** `min_prefix_len` and `min_word_len` and in **footer.php** and **footer_excerpts.php** the `minLength` ( which trigger firing the ajax call).  

Live demo with Sphinx :   
-------------------------------------------  
http://demos.sphinxsearch.com/SphinxAutocompleteExample/
License:
-------------------------------------------
Sphinx Samples  is free software, and is released under the terms of the GPL version 2 or (at your option) any later version.
Manticore website : https://manticoresearch.com/
Manticore repository : https://github.com/manticoresoftware/manticore
Sphinx website : http://sphinxsearch.com/  
Sphinx read-only repository :https://github.com/sphinxsearch/sphinx
