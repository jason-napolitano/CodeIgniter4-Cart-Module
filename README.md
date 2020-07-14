# CodeIgniter4-Cart-Module
This is a `composer` installable, CodeIgniter 4 module that is nearly a direct port of the Codeigniter 3 Cart Library Class.
Of course, this has been mildly updated and is consistent with the new version of the framework.

This means that this library, instead of just being a _class_ that you can use in your projects, it
has been updated with Namespaces, been refactored to adhere to the CodeIgniter style guide 
and also has been built to use CodeIgniter 4's Service Container to allow for a shared Cart instance 
across your application.  

More detailed information can be found [here](https://codeigniter.com/userguide3/libraries/cart.html). Please
note that the documentation is for the CodeIgniter 3 library but the fundamentals and inner workings of the 
library are still nearly identical.

## Installation:
 - Install via composer `composer install jason-napolitano/codeigniter4-cart-module`
 - Add it to the `$psr4` array in `app/Config/Autoload.php`:
 ```php
$psr4 = [
    'CodeIgniterCart' => ROOTPATH . 'vendor/jason-napolitano/codeigniter4-cart-module/src'

    // OTHER PSR4 ENTRIES
];
``` 
  
## Usage
 ```php
 // Call the cart service using the helper function
 $cart = \Config\Services::cart();
 
 // Insert an array of values
 $cart->insert(array(
    'id'      => 'sku_1234ABCD',
    'qty'     => 1,
    'price'   => '19.56',
    'name'    => 'T-Shirt',
    'options' => array('Size' => 'L', 'Color' => 'Red')
));
 
 // Update an array of values
 $cart->update(array(
    'rowid'   => '4166b0e7fc8446e81e16883e9a812db8',
    'id'      => 'sku_1234ABCD',
    'qty'     => 3,
    'price'   => '24.89',
    'name'    => 'T-Shirt',
    'options' => array('Size' => 'L', 'Color' => 'Red')
));

// Get the total items
$cart->totalItems();

// Remove an item using its `rowid`
$cart->remove('4166b0e7fc8446e81e16883e9a812db8');
   
// Clear the shopping cart
$cart->destroy();

// Get the cart contents as an array
$cart->contents();
```
 
## License:
 MIT License

Copyright (c) 2019 Jason Napolitano

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
