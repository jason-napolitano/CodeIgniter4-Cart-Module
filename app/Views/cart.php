<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

<section class="container">
    <div class="row">
        <div class="col">
            <table class="table table-bordered table-striped">

                <tr>
                    <th>QTY</th>
                    <th>Item Description</th>
                    <th style="text-align:right">Item Price</th>
                    <th style="text-align:right">Sub-Total</th>
                </tr>
                <?php $i = 1; ?>
                <?php foreach ( $cart->contents() as $items ): ?>
                    <tr>
                        <td>
                            <?= $items[ 'qty' ]; ?>
                        </td>
                        <td>
                            <?php echo $items[ 'name' ]; ?>

                            <?php if ( $cart->hasOptions($items[ 'rowid' ]) === true ): ?>

                                <p>
                                    <?php foreach ( $cart->productOptions($items[ 'rowid' ]) as $option_name => $option_value ): ?>

                                        <strong><?php echo $option_name; ?>:</strong> <?php echo $option_value; ?><br/>

                                    <?php endforeach; ?>
                                </p>

                            <?php endif; ?>

                        </td>
                        <td style="text-align:right"><?php echo $cart->formatNumber($items[ 'price' ]); ?></td>
                        <td style="text-align:right">$<?php echo $cart->formatNumber($items[ 'subtotal' ]); ?></td>
                    </tr>

                    <?php $i++; ?>

                <?php endforeach; ?>

                <tr>
                    <td colspan="2"></td>
                    <td class="right"><strong>Total</strong></td>
                    <td class="right">$<?php echo $cart->formatNumber($cart->total()); ?></td>
                </tr>

            </table>
        </div>
    </div>
</section>
