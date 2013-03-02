<?php

/*** CELLDB
config.php - global include file containing user-defined settings

created 2005-10-10 - SVD - sprouted out of celldb.php

***/

  // define constants for connecting to the database
  $dbserver="localhost";
  $dbuser="celldb_user";
  $dbpassword="celldb_123";
  $dbname="cell";
  
  // lab says which group of users / animals should be shown automatically
  $LAB="mylabname";
  
  // display units for animal weights
  $weightunits="g";     // little animals
  //$weightunits="kg";  // big animals

  // SETTINGS BELOW ARE DEPRCATED OR UNDER DEVELOPMENT. CHANGE AT YOUR OWN RISK!
  
  // if $ypaccounts is set to 1, then check passwords using "ypmatch"
  // otherwise, store passwords in the database and accounts need not 
  // exist on the server itself
  $ypaccounts=0;
  
  // $view determines whether interface should be oriented toward visual
  // or auditory experiments.  same database, different interface
  $view="ear";  // use this for auditory data (NOT ACTIVE, use "")
  //$view="eye";  // use this for visual data


  //version 0.3: added multi-unit per recording site support!
  // version 0.4, moved over to NSL, own user management system
  //              what to do about different types of physiology data?
  // version 0.5  added alternate views
  // version 0.6  added setup scripts
  $siteinfo="CELLDB v0.6";

?>
