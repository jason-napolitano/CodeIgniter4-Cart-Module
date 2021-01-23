<?php namespace CodeIgniterCart;

/**
* The Cart class is a basic port of the CodeIgniter 3 cart module for CodeIgniter 4.
*
* @package    CodeIgniterCart
* @copyright  Copyright (c) 2014 - 2017, British Columbia Institute of Technology (http://bcit.ca/)
* @copyright  Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
* @link       https://github.com/jason-napolitano/CodeIgniter4-Cart-Module
* @link       https://codeigniter.com/user_guide/libraries/cart.html
* @license    http://opensource.org/licenses/MIT
* @author     EllisLab Dev Team
* @since      1.0.0
*/
class Cart
{
    /**
     * These are the regular expression rules that we use to validate the product ID and product name
     * alpha-numeric, dashes, underscores, or periods
     *
     * @var string
     */
    public $productIdRules = '\.a-z0-9_-';

    /**
     * These are the regular expression rules that we use to validate the product ID and product name
     * alpha-numeric, dashes, underscores, colons or periods
     *
     * @var string
     */
    public $productNameRules = '\w \-\.\:';

    /**
     * only allow safe product names
     *
     * @var bool
     */
    public $productNameSafe = true;

    /**
     * Contents of the cart
     *
     * @var array
     */
    protected $cartContents = [];

    /**
     * Session Service
     *
     * @var \CodeIgniter\Session\Session $session
     */
    protected $session;

    // ------------------------------------------------------------------------

    /**
     * Shopping Class Constructor
     *
     * The constructor loads the Session class, used to store the shopping cart contents.
     */
    public function __construct()
    {
        $this->session = session();

        // Grab the shopping cart array from the session table
        $this->cartContents = $this->session->get('cart_contents');
        if ( $this->cartContents === null ) {
            // No cart exists so we'll set some base values
            $this->cartContents = [ 'cart_total' => 0, 'total_items' => 0 ];
        }

        log_message('info', 'Cart Class Initialized');
    }

    // --------------------------------------------------------------------

    /**
     * Insert items into the cart and save it to the session table
     *
     * @param array $items
     *
     * @return bool
     */
    public function insert($items = []): bool
    {
        // Was any cart data passed? No? Bah...
        if ( ! is_array($items) || count($items) === 0 ) {
            log_message('error', 'The insert method must be passed an array containing data.');
            return false;
        }

        // You can either insert a single product using a one-dimensional array,
        // or multiple products using a multi-dimensional one. The way we
        // determine the array type is by looking for a required array key named "id"
        // at the top level. If it's not found, we will assume it's a multi-dimensional array.

        $save_cart = false;
        if ( isset($items[ 'id' ]) ) {
            if ( ( $rowid = $this->_insert($items) ) ) {
                $save_cart = true;
            }
        } else {
            foreach ( $items as $val ) {
                if ( is_array($val) && isset($val[ 'id' ]) && $this->_insert($val) ) {
                    $save_cart = true;
                }
            }
        }

        // Save the cart data if the insert was successful
        if ( $save_cart === true ) {
            $this->saveCart();
            return $rowid ?? true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Insert
     *
     * @param array $items
     * @return bool|string
     */
    protected function _insert($items = [])
    {
        // Was any cart data passed? No? Bah...
        if ( ! is_array($items) || count($items) === 0 ) {
            log_message('error', 'The insert method must be passed an array containing data.');
            return false;
        }

        // --------------------------------------------------------------------

        // Does the $items array contain an id, quantity, price, and name?  These are required
        if ( ! isset($items[ 'id' ], $items[ 'qty' ], $items[ 'price' ], $items[ 'name' ]) ) {
            log_message('error', 'The cart array must contain a product ID, quantity, price, and name.');
            return false;
        }

        // --------------------------------------------------------------------

        // Prep the quantity. It can only be a number.  Duh... also trim any leading zeros
        $items[ 'qty' ] = (float)$items[ 'qty' ];

        // If the quantity is zero or blank there's nothing for us to do
        if ( $items[ 'qty' ] === 0 ) {
            return false;
        }

        // --------------------------------------------------------------------

        // Validate the product ID. It can only be alpha-numeric, dashes, underscores or periods
        // Not totally sure we should impose this rule, but it seems prudent to standardize IDs.
        // Note: These can be user-specified by setting the $this->product_id_rules variable.
        if ( ! preg_match('/^[' . $this->productIdRules . ']+$/i', $items[ 'id' ]) ) {
            log_message('error', 'Invalid product ID.  The product ID can only contain alpha-numeric characters, dashes, and underscores');
            return false;
        }

        // --------------------------------------------------------------------

        // Validate the product name. It can only be alpha-numeric, dashes, underscores, colons or periods.
        // Note: These can be user-specified by setting the $this->product_name_rules variable.
        if ( $this->productNameSafe && ! preg_match('/^[' . $this->productNameRules . ']+$/i' . ( true ? 'u' : '' ), $items[ 'name' ]) ) {
            log_message('error', 'An invalid name was submitted as the product name: ' . $items[ 'name' ] . ' The name can only contain alpha-numeric characters, dashes, underscores, colons, and spaces');
            return false;
        }

        // --------------------------------------------------------------------

        // Prep the price. Remove leading zeros and anything that isn't a number or decimal point.
        $items[ 'price' ] = (float)$items[ 'price' ];

        // We now need to create a unique identifier for the item being inserted into the cart.
        // Every time something is added to the cart it is stored in the master cart array.
        // Each row in the cart array, however, must have a unique index that identifies not only
        // a particular product, but makes it possible to store identical products with different options.
        // For example, what if someone buys two identical t-shirts (same product ID), but in
        // different sizes?  The product ID (and other attributes, like the name) will be identical for
        // both sizes because it's the same shirt. The only difference will be the size.
        // Internally, we need to treat identical submissions, but with different options, as a unique product.
        // Our solution is to convert the options array to a string and MD5 it along with the product ID.
        // This becomes the unique "row ID"
        if ( isset($items[ 'options' ]) && count($items[ 'options' ]) > 0 ) {
            $rowid = md5($items[ 'id' ] . serialize($items[ 'options' ]));
        } else {
            // No options were submitted so we simply MD5 the product ID.
            // Technically, we don't need to MD5 the ID in this case, but it makes
            // sense to standardize the format of array indexes for both conditions
            $rowid = md5($items[ 'id' ]);
        }

        // --------------------------------------------------------------------

        // Now that we have our unique "row ID", we'll add our cart items to the master array
        // grab quantity if it's already there and add it on
        $old_quantity = isset($this->cartContents[ $rowid ][ 'qty' ]) ? (int)$this->cartContents[ $rowid ][ 'qty' ] : 0;

        // Re-create the entry, just to make sure our index contains only the data from this submission
        $items[ 'rowid' ] = $rowid;
        $items[ 'qty' ] += $old_quantity;
        $this->cartContents[ $rowid ] = $items;

        return $rowid;
    }

    // ------------------------------------------------------------------------

    /**
     * Update the cart
     *
     * This function permits the quantity of a given item to be changed.
     * Typically it is called from the "view cart" page if a user makes
     * changes to the quantity before checkout. That array must contain the
     * product ID and quantity for each item.
     *
     * @param array $items
     * @return bool
     */
    public function update($items = []): bool
    {
        // Was any cart data passed?
        if ( ! is_array($items) || count($items) === 0 ) {
            return false;
        }

        // You can either update a single product using a one-dimensional array,
        // or multiple products using a multi-dimensional one.  The way we
        // determine the array type is by looking for a required array key named "rowid".
        // If it's not found we assume it's a multi-dimensional array
        $save_cart = false;
        if ( isset($items[ 'rowid' ]) ) {
            if ( $this->_update($items) === true ) {
                $save_cart = true;
            }
        } else {
            foreach ( $items as $val ) {
                if ( is_array($val) && isset($val[ 'rowid' ]) && $this->_update($val) === true ) {
                    $save_cart = true;
                }
            }
        }

        // Save the cart data if the insert was successful
        if ( $save_cart === true ) {
            $this->saveCart();
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Update the cart
     *
     * This function permits changing item properties.
     * Typically it is called from the "view cart" page if a user makes
     * changes to the quantity before checkout. That array must contain the
     * rowid and quantity for each item.
     *
     * @param array $items
     * @return bool
     */
    protected function _update($items = []): bool
    {
        // Without these array indexes there is nothing we can do
        if ( ! isset($items[ 'rowid' ], $this->cartContents[ $items[ 'rowid' ] ]) ) {
            return false;
        }

        // Prep the quantity
        if ( isset($items[ 'qty' ]) ) {
            $items[ 'qty' ] = (float)$items[ 'qty' ];
            // Is the quantity zero?  If so we will remove the item from the cart.
            // If the quantity is greater than zero we are updating
            if ( $items[ 'qty' ] === 0 ) {
                unset($this->cartContents[ $items[ 'rowid' ] ]);
                return true;
            }
        }

        // find updatable keys
        $keys = array_intersect(array_keys($this->cartContents[ $items[ 'rowid' ] ]), array_keys($items));
        // if a price was passed, make sure it contains valid data
        if ( isset($items[ 'price' ]) ) {
            $items[ 'price' ] = (float)$items[ 'price' ];
        }

        // product id & name shouldn't be changed
        foreach ( array_diff($keys, [ 'id', 'name' ]) as $key ) {
            $this->cartContents[ $items[ 'rowid' ] ][ $key ] = $items[ $key ];
        }

        return true;
    }

    // ------------------------------------------------------------------------

    /**
     * Save the cart array to the session DB
     *
     * @return bool
     */
    protected function saveCart(): bool
    {
        // Let's add up the individual prices and set the cart sub-total
        $this->cartContents[ 'total_items' ] = $this->cartContents[ 'cart_total' ] = 0;
        foreach ( $this->cartContents as $key => $val ) {
            // We make sure the array contains the proper indexes
            if ( ! is_array($val) || ! isset($val[ 'price' ], $val[ 'qty' ]) ) {
                continue;
            }

            $this->cartContents[ 'cart_total' ] += ( $val[ 'price' ] * $val[ 'qty' ] );
            $this->cartContents[ 'total_items' ] += $val[ 'qty' ];
            $this->cartContents[ $key ][ 'subtotal' ] = ( $this->cartContents[ $key ][ 'price' ] * $this->cartContents[ $key ][ 'qty' ] );
        }

        // Is our cart empty? If so we delete it from the session
        if ( count($this->cartContents) <= 2 ) {
            $this->session->remove('cart_contents');

            // Nothing more to do... coffee time!
            return false;
        }

        // If we made it this far it means that our cart has data.
        // Let's pass it to the Session class so it can be stored
        $this->session->set('cart_contents', $this->cartContents);

        // Woot!
        return true;
    }

    // ------------------------------------------------------------------------

    /**
     * Cart Total
     *
     * @return mixed
     */
    public function total()
    {
        return $this->cartContents[ 'cart_total' ];
    }

    // ------------------------------------------------------------------------

    /**
     * Remove Item
     *
     * Removes an item from the cart
     *
     * @param $rowid
     *
     * @return bool
     */
    public function remove($rowid): bool
    {
        // unset & save
        unset($this->cartContents[ $rowid ]);
        $this->saveCart();
        return true;
    }

    // ------------------------------------------------------------------------

    /**
     * Total Items
     *
     * Returns the total item count
     *
     * @return mixed
     */
    public function totalItems()
    {
        return $this->cartContents[ 'total_items' ];
    }

    // ------------------------------------------------------------------------

    /**
     * Cart Contents
     *
     * Returns the entire cart array
     *
     * @param bool $newest_first
     * @return array
     */
    public function contents($newest_first = false): array
    {
        // do we want the newest first?
        $cart = ( $newest_first ) ? array_reverse($this->cartContents) : $this->cartContents;

        // Remove these so they don't create a problem when showing the cart table
        unset($cart[ 'total_items' ], $cart[ 'cart_total' ]);

        return $cart;
    }

    // ------------------------------------------------------------------------

    /**
     * Get cart item
     *
     * Returns the details of a specific item in the cart
     *
     * @param $row_id
     * @return bool|mixed
     */
    public function getItem($row_id)
    {
        return ( in_array($row_id, [ 'total_items', 'cart_total' ], true) OR ! isset($this->cartContents[ $row_id ]) )
            ? false
            : $this->cartContents[ $row_id ];
    }

    // ------------------------------------------------------------------------

    /**
     * Has options
     *
     * Returns TRUE if the rowid passed to this function correlates to an item
     * that has options associated with it.
     *
     * @param string $row_id
     * @return bool
     */
    public function hasOptions($row_id = ''): bool
    {
        return ( isset($this->cartContents[ $row_id ][ 'options' ]) && count($this->cartContents[ $row_id ][ 'options' ]) !== 0 );
    }

    // ------------------------------------------------------------------------

    /**
     * Product options
     *
     * Returns the an array of options, for a particular product row ID
     *
     * @param string $row_id
     * @return array|mixed
     */
    public function productOptions($row_id = '')
    {
        return $this->cartContents[ $row_id ][ 'options' ] ?? [];
    }

    // ------------------------------------------------------------------------

    /**
     * Format Number
     *
     * Returns the supplied number with commas and a decimal point.
     *
     * @param string $n
     * @return string
     */
    public function formatNumber($n = ''): string
    {
        return ( $n === '' ) ? '' : number_format((float)$n, 2);
    }

    // ------------------------------------------------------------------------

    /**
     * Destroy the cart
     *
     * Empties the cart and kills the session
     */
    public function destroy(): void
    {
        $this->cartContents = [ 'cart_total' => 0, 'total_items' => 0 ];
        $this->session->remove('cart_contents');
    }
}
