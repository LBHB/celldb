<?php

/*** CELLDB
config.php - global include file containing user-defined settings

created 2005-10-10 - SVD - sprouted out of celldb.php

***/

  // define constants for connecting to the database
  $dbserver="localhost";
  $dbuser="svd_david";
  $dbpassword="nine1997";
  $dbname="svd_cell_yale";
  
  // if $ypaccounts is set to 1, then check passwords using "ypmatch"
  // otherwise, store passwords in the database and accounts need not 
  // exist on the server itself
  $ypaccounts=0;
  
  // $view determines whether interface should be oriented toward visual
  // or auditory experiments.  same database, different interface
  $view="ear";  // use this for auditory data (NOT ACTIVE, use "")
  //$view="eye";  // use this for visual data

  // lab says which group of users / animals should be shown automatically
  $LAB="yale";
  
  // display units for animal weights
  $weightunits="g";     // little animals
  //$weightunits="kg";  // big animals

  //version 0.3: added multi-unit per recording site support!
  // version 0.4, moved over to NSL, own user management system
  //              what to do about different types of physiology data?
  // version 0.5  added alternate views
  // version 0.6  added setup scripts
  $siteinfo="CELLDB v0.6";

?>
