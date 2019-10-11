<section style="width: 965px">
    <table cellpadding="6" cellspacing="1" style="width:100%" border="0">

        <tr>
            <th>QTY</th>
            <th>Item Description</th>
            <th style="text-align:right">Item Price</th>
            <th style="text-align:right">Sub-Total</th>
        </tr>
        <?php $i = 1; ?>
        <?php foreach ($cart->contents() as $items): ?>
            <?php echo form_hidden($i.'[rowid]', $items['rowid']); ?>
            <tr>
                <td><?php echo form_input(array('name' => $i.'[qty]', 'value' => $items['qty'], 'maxlength' => '3', 'size' => '5')); ?></td>
                <td>
                    <?php echo $items['name']; ?>

                    <?php if ($cart->hasOptions($items['rowid']) == TRUE): ?>

                        <p>
                            <?php foreach ($cart->productOptions($items['rowid']) as $option_name => $option_value): ?>

                                <strong><?php echo $option_name; ?>:</strong> <?php echo $option_value; ?><br />

                            <?php endforeach; ?>
                        </p>

                    <?php endif; ?>

                </td>
                <td style="text-align:right"><?php echo $cart->formatNumber($items['price']); ?></td>
                <td style="text-align:right">$<?php echo $cart->formatNumber($items['subtotal']); ?></td>
            </tr>

            <?php $i++; ?>

        <?php endforeach; ?>

        <tr>
            <td colspan="2"> </td>
            <td class="right"><strong>Total</strong></td>
            <td class="right">$<?php echo $cart->formatNumber($cart->total()); ?></td>
        </tr>

    </table>

</section>