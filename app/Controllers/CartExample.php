<?php namespace App\Controllers;

class HomeController extends \CodeIgniter\Controller
{
    public function index()
    {
        $data = [ 'cart' => cart() ];
        echo view('cart', $data);
    }

    public function addOne()
    {
        $cart = cart();
        $data = array(
            'id'      => 'sku_1234ABCD',
            'qty'     => 1,
            'price'   => '19.56',
            'name'    => 'T-Shirt',
            'options' => array('Size' => 'L', 'Color' => 'Red')
        );
        $cart->insert($data);
    }

    //--------------------------------------------------------------------

    public function addMultiple()
    {
        $cart = cart();
        $data = array(
            array(
                'id'      => 'sku_123ABC',
                'qty'     => 1,
                'price'   => 39.95,
                'name'    => 'T-Shirt',
                'options' => array('Size' => 'L', 'Color' => 'Red')
            ),
            array(
                'id'      => 'sku_567ZYX',
                'qty'     => 1,
                'price'   => 9.95,
                'name'    => 'Coffee Mug'
            ),
            array(
                'id'      => 'sku_965QRS',
                'qty'     => 1,
                'price'   => 29.95,
                'name'    => 'Shot Glass'
            )
        );
        $cart->insert($data);
    }

    //--------------------------------------------------------------------

    public function destroy()
    {
        $cart = cart();
        $cart->destroy();
    }

    //--------------------------------------------------------------------

}
