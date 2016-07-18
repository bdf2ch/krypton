<?php if (!isset($redirect)) { ?>


<br><div class="table-responsive">
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <td class="text-left"><?php echo $column_name; ?></td>
        <td class="text-left"><?php echo $column_model; ?></td>
        <td class="text-right"><?php echo $column_quantity; ?></td>
        <td class="text-right"><?php echo $column_price; ?></td>
        <td class="text-right"><?php echo $column_total; ?></td>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $product) { ?>
      <tr>
        <td class="text-left"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
          <?php foreach ($product['option'] as $option) { ?>
          <!-- <br /> -->
          &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
          <?php } ?>
          <?php if($product['recurring']) { ?>
          <!-- <br /> -->
          <span class="label label-info"><?php echo $text_recurring_item; ?></span> <small><?php echo $product['recurring']; ?></small>
          <?php } ?></td>
        <td class="text-left"><?php echo $product['model']; ?></td>
        <td class="text-right"><?php echo $product['quantity']; ?></td>
        <td class="text-right"><?php echo $product['price']; ?></td>
        <td class="text-right"><?php echo $product['total']; ?></td>
      </tr>
      <?php } ?>
      <?php foreach ($vouchers as $voucher) { ?>
      <tr>
        <td class="text-left"><?php echo $voucher['description']; ?></td>
        <td class="text-left"></td>
        <td class="text-right">1</td>
        <td class="text-right"><?php echo $voucher['amount']; ?></td>
        <td class="text-right"><?php echo $voucher['amount']; ?></td>
      </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <?php foreach ($totals as $total) { ?>
      <tr>
        <td colspan="4" class="text-right"><strong><?php echo $total['title']; ?>:</strong></td>
        <td class="text-right"><?php echo $total['text']; ?></td>
      </tr>
      <?php } ?>
    </tfoot>
  </table>

    <?php
//ROBOKASSA START
        $orderid = $_SESSION['last_id']*1000;
        $total = $_SESSION['total_price'];

        $name = 'Macarena';
        $robopass='macarenarobokassa1808';

        $SignatureValue = md5($name. ":" .$total. ":" .$orderid. ":" .$robopass);

    ?>
    <br/>  <br/>

 <div style="display: none;"><?php $payment_method ?></div>

    <div id="robokassa_thanks_block" style="display:<?php echo($payment_method != 'ROBOKASSA' ? 'block' : 'none') ?> ;">
	Вы выбрали способ оплаты "наличными" при получении заказа, но если вы изменили решение, то все еще можете оплатить его через систему Robokassa

        <br/>  <!-- <br/> -->

        <form action="https://auth.robokassa.ru/Merchant/Index.aspx" method="POST">
            <input type="hidden" name="MrchLogin" value="Macarena">
            <input type="hidden" name="OutSum" value="<?=$total?>">
            <input type="hidden" name="InvId" value="<?=$orderid;?>">
            <input type="hidden" name="Desc" value="Оплата заказа <?=$orderid;?>">
            <input type="hidden" name="SignatureValue" value="<?=$SignatureValue;?>">
            <input type="hidden" name="IncCurrLabel" value="">
            <input type="hidden" name="Culture" value="ru">

            <input type="submit" value="Оплатить" style="padding: 10px 20px;">
        </form>
    </div>
    <p class='voucher_price' style="visibility: hidden"><?php echo preg_replace("/[^0-9]/", '', $total['text']); ?></p>



    </div>
<!-- </div> -->



<!-- <?php
//SIMPLEPAY START

        $outlet_id = '1363';
        $secret_key= 'daebb5875a89dbc3fee1dec200ef5bb2';

    ?>
-->



<!--
    <br/>  <br/>
	
    <div id="simplepay_thanks_block">
  Также вы можете оплатить его через систему SimplePay
-->
        <br/>  <!-- <br/> -->
<!--
        <form id="spform" action="/simplepay/pay.php" method="POST">
            <input type="hidden" name="sp_amount" value="<?=$total?>">
            <input type="hidden" name="sp_order_id" value="<?=$orderid;?>">
            <input type="hidden" name="sp_description" value="Оплата заказа <?=$orderid;?>">

            <a href="#" onclick="document().getElementById('#spform').submit()"><button value="Оплатить" style="padding: 10px 20px;">Оплатить</button></a>
        </form>
    </div>
    <p class='voucher_price' style="visibility: hidden"><?php echo preg_replace("/[^0-9]/", '', $total['text']); ?></p>




</div>
-->
<?php echo $payment; ?>
<?php } else { ?>
<script type="text/javascript"><!--
location = '<?php echo $redirect; ?>';
//--></script>
<?php } ?>

