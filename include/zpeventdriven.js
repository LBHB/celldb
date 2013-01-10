// $Id: zpeventdriven.js 4322 2006-09-04 08:49:33Z shacka $
/**
 * @fileoverview Zapatec EventDriven library. Base EventDriven class. Contains
 * functions to handle events and basic methods for event-driven class.
 *
 * <pre>
 * Copyright (c) 2004-2006 by Zapatec, Inc.
 * http://www.zapatec.com
 * 1700 MLK Way, Berkeley, California,
 * 94709, U.S.A.
 * All rights reserved.
 * </pre>
 */

if (typeof Zapatec == 'undefined') {
  /**
   * @ignore Namespace definition.
   */
  Zapatec = {};
}

/**
 * Base event-driven class.
 *
 * @constructor
 */
Zapatec.EventDriven = function() {};

/**
 * Initializes object.
 */
Zapatec.EventDriven.prototype.init = function() {
  // Holds events of this object
  this.events = {};
};

/**
 * Adds event listener.
 *
 * @param {string} strEvent Event name
 * @param {function} funcListener Event listener
 * @param {function} first If true funcListener will be put as the first one
 */
Zapatec.EventDriven.prototype.
 addEventListener = function(strEvent, funcListener, first) {
  if (typeof funcListener != "function") {
    return false;
  }
  if (!this.events[strEvent]) {
    this.events[strEvent] = {
      listeners: []
    };
  }
  if (!first) {
    this.events[strEvent].listeners.push(funcListener);
  } else {
    this.events[strEvent].listeners.unshift(funcListener);
  }
};

/**
 * Removes event listener.
 *
 * @param {string} strEvent Event name
 * @param {function} funcListener Event listener
 */
Zapatec.EventDriven.prototype.
 removeEventListener = function(strEvent, funcListener) {
  if (!this.events[strEvent]) {
    return;
  }
  var arrListeners = this.events[strEvent].listeners;
  for (var iListener = 0; iListener < arrListeners.length; iListener++) {
    if (arrListeners[iListener] == funcListener) {
      arrListeners.splice(iListener, 1);
      return;
    }
  }
};

/**
 * Checks if the event exists.
 *
 * @param {string} strEvent Event name
 * @return Exists
 * @type boolean
 */
Zapatec.EventDriven.prototype.isEvent = function(strEvent) {
  if (this.events[strEvent]) {
    return true;
  }
  return false;
};

/**
 * Removes all listeners for the event.
 *
 * @param {string} strEvent Event name
 */
Zapatec.EventDriven.prototype.removeEvent = function(strEvent) {
  if (this.events[strEvent]) {
    var undef;
    this.events[strEvent] = undef;
  }
};

/**
 * Fires event. Takes in one mandatory argument strEvent and optionally
 * any number of other arguments that should be passed to the listeners.
 *
 * @param {string} strEvent Event name
 */
Zapatec.EventDriven.prototype.fireEvent = function(strEvent) {
  if (!this.events[strEvent]) {
    return;
  }
  var arrListeners = this.events[strEvent].listeners;
  for (var iListener = 0; iListener < arrListeners.length; iListener++) {
    // Remove first argument
    var arrArgs = [].slice.call(arguments, 1);
    // Call in scope of this object
    arrListeners[iListener].apply(this, arrArgs);
  }
};

/**
 * Holds static events.
 * @private
 */
Zapatec.EventDriven.events = {};

/**
 * Adds event listener to static event.
 *
 * @param {string} strEvent Event name
 * @param {function} funcListener Event listener
 */
Zapatec.EventDriven.addEventListener = function(strEvent, funcListener) {
  if (typeof funcListener != "function") {
    return false;
  }
  if (!Zapatec.EventDriven.events[strEvent]) {
    Zapatec.EventDriven.events[strEvent] = {
      listeners: []
    };
  }
  Zapatec.EventDriven.events[strEvent].listeners.push(funcListener);
};

/**
 * Removes event listener from static event.
 *
 * @param {string} strEvent Event name
 * @param {function} funcListener Event listener
 */
Zapatec.EventDriven.removeEventListener = function(strEvent, funcListener) {
  if (!Zapatec.EventDriven.events[strEvent]) {
    return;
  }
  var arrListeners = Zapatec.EventDriven.events[strEvent].listeners;
  for (var iListener = 0; iListener < arrListeners.length; iListener++) {
    if (arrListeners[iListener] == funcListener) {
      arrListeners.splice(iListener, 1);
      return;
    }
  }
};

/**
 * Checks if the static event exists.
 *
 * @param {string} strEvent Event name
 * @return Exists
 * @type boolean
 */
Zapatec.EventDriven.isEvent = function(strEvent) {
  if (Zapatec.EventDriven.events[strEvent]) {
    return true;
  }
  return false;
};

/**
 * Removes all listeners for the static event.
 *
 * @param {string} strEvent Event name
 */
Zapatec.EventDriven.removeEvent = function(strEvent) {
  if (Zapatec.EventDriven.events[strEvent]) {
    var undef;
    Zapatec.EventDriven.events[strEvent] = undef;
  }
};

/**
 * Fires static event. Takes in one mandatory argument strEvent and optionally
 * any number of other arguments that should be passed to the listeners.
 *
 * @param {string} strEvent Event name
 */
Zapatec.EventDriven.fireEvent = function(strEvent) {
  if (!Zapatec.EventDriven.events[strEvent]) {
    return;
  }
  var arrListeners = Zapatec.EventDriven.events[strEvent].listeners;
  for (var iListener = 0; iListener < arrListeners.length; iListener++) {
    // Remove first argument
    var arrArgs = [].slice.call(arguments, 1);
    // Call listener
    arrListeners[iListener].apply(arrListeners[iListener], arrArgs);
  }
};
