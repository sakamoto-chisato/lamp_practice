<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入明細</title>
  <link rel="stylesheet" href="<?php print(h(STYLESHEET_PATH . 'admin.css')); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>

  <div class="container">
    <h1>購入明細</h1>
    <?php include VIEW_PATH . 'templates/messages.php'; ?>
    <div class="row">
      <div class="col-sm">
        <h4>注文番号：<?php print(h($purchased_id)); ?> </h4>
      </div>
      <div class="col-6">
        <h4>注文日時：<?php print(h($values[0]['purchased_datetime'])); ?></h4>
      </div>
      <div class="col-sm">
        <h4 class="text-right">合計金額: <?php print(h(number_format($total))); ?>円</h4>
      </div>
    </div>
    <table class="table table-bordered text-center">
      <thead class="thead-light">
        <tr>
          <th>商品名</th>
          <th>価格</th>
          <th>購入数</th>
          <th>小計</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($values as $value){ ?>
        <tr>
          <td><?php print(h($value['purchased_name'])); ?></td>
          <td><?php print(h(number_format($value['purchased_price']))); ?>円</td>
          <td>
            <?php print(h($value['purchased_amount'])); ?>個
          </td>
          <td><?php print(h(number_format($value['d.purchased_price * d.purchased_amount']))); ?>円</td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>