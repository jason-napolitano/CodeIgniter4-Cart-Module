# CodeIgniter4-Cart-Module
A basic port of the CodeIgniter 3 cart module for CodeIgniter 4. With the 
CodeIgniter 3 Cart module being officially dropped, I wanted to implement 
it in CodeIgniter 4. This is a small example of that. This is not truly 
'supported' since I just kinda threw it together on a whim for a project 
that arose, but feel free to contact me regarding it or offer PR's for 
any improvements and/or corrections to the existing codebase.

## Installation:
 - Install via composer `composer install jason-napolitano/codeigniter4-cart-module` 
## Usage
 - More detailed information can be found [here](https://codeigniter.com/userguide3/libraries/cart.html)
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
