/**
 * File: extended-wrapper.jsx
 * Author: Antoine87
 */

// json2 library (https://github.com/douglascrockford/JSON-js)
/*{{ json_lib }}*/

// Public functions accessible to the client script
/*{{ public_functions }}*/


// Get the original client script passed as argument by the PHP wrapper
var __script__ = app.scriptArgs.getValue('__script__');

// Execute the client script and return the result to the PHP wrapper
try {
    _toString({
        success: true,
        result: eval(__script__)
    });

// Catch any exception thrown by the client script preventing the server's log to print them
// and return it to the PHP wrapper to be thrown to the service's caller
} catch (e) {
    _toString({
        success: false,
        exception: e
    });
}
