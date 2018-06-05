/**
 * File: test.jsx
 * Author: Antoine87
 */

// Get the value of the argument 'test' passed to this script
var arg = app.scriptArgs.getValue('test');

ret = {
    arg: arg,
    int: 1234,
    str: 'string',
    arr: [
        'test',
        123
    ]
};

// Return the value of the 'test' argument
ret;
