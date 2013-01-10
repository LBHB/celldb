/**
 * @fileoverview Loads generic modules required for all widgets.
 *
 * <pre>
 * Copyright (c) 2004-2006 by Zapatec, Inc.
 * http://www.zapatec.com
 * 1700 MLK Way, Berkeley, California,
 * 94709, U.S.A.
 * All rights reserved.
 * </pre>
 */

/* $Id: zapatec.js 4908 2006-10-25 15:19:32Z alex $ */

if (typeof Zapatec == 'undefined') {
  /**
   * Namespace definition.
   * @constructor
   */
  Zapatec = function() {};
}

/**
 * Zapatec Suite version.
 * @private
 */
Zapatec.version = '2.1';

/**
 * Path to main Zapatec script.
 * @private
 */
Zapatec.zapatecPath = function() {
  // Get all script elements
  var arrScripts = document.getElementsByTagName('script');
  // Find the script in the list
  for (var iScript = arrScripts.length - 1; iScript >= 0; iScript--) {
    var strSrc = arrScripts[iScript].getAttribute('src');
    if (!strSrc) {
      continue;
    }
    var arrTokens = strSrc.split('/');
    // Remove last token
    var strLastToken;
    if (Array.prototype.pop) {
      strLastToken = arrTokens.pop();
    } else {
      // IE 5
      strLastToken = arrTokens[arrTokens.length - 1];
      arrTokens.length -= 1;
    }
    if (strLastToken == 'zapatec.js') {
      return arrTokens.length ? arrTokens.join('/') + '/' : '';
    }
  }
  // Not found
  return '';
} ();

// For backward compatibility
if (Zapatec.version == '2.1.comp') {
  Zapatec.zapatecPath = Zapatec.zapatecPath.replace(/utils\/$/, '../utils/');
}

/**
 * Simply writes script tag to the document.
 *
 * <pre>
 * If special Zapatec.doNotInclude flag is set, this function does nothing.
 * </pre>
 *
 * @private
 * @param {string} strSrc Src attribute value of the script element
 * @param {string} strId Optional. Id of the script element
 */
Zapatec.include = function(strSrc, strId) {
  // Check flag
  if (Zapatec.doNotInclude) {
    return;
  }
  // Include file
  document.write('<script type="text/javascript" src="' + 
   strSrc + (typeof strId == 'string' ? '" id="' + strId : '') + '"></script>');
};

// Include required scripts
Zapatec.include(Zapatec.zapatecPath + 'utils.js', 'Zapatec.Utils');
Zapatec.include(Zapatec.zapatecPath + 'zpeventdriven.js', 'Zapatec.EventDriven');
Zapatec.include(Zapatec.zapatecPath + 'transport.js', 'Zapatec.Transport');
Zapatec.include(Zapatec.zapatecPath + 'zpwidget.js', 'Zapatec.Widget');
//Zapatec.include('utils.js', 'Zapatec.Utils');
//Zapatec.include('zpeventdriven.js', 'Zapatec.EventDriven');
//Zapatec.include('transport.js', 'Zapatec.Transport');
//Zapatec.include('zpwidget.js', 'Zapatec.Widget');
