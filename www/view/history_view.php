<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入履歴</title>
  <link rel="stylesheet" href="<?php print(h(STYLESHEET_PATH . 'admin.css')); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>

  <div class="container">
    <h1>購入履歴</h1>
    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <table class="table table-bordered text-center">
      <thead class="thead-light">
        <tr>
          <th>注文番号</th>
          <th>購入日時</th>
          <th>合計金額</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($values as $value){ ?>
        <tr>
          <td><?php print(h($value['purchased_id'])); ?></td>
          <td><?php print(h($value['purchased_datetime'])); ?></td>
          <td><?php print(h(number_format($value['SUM(d.purchased_price * d.purchased_amount)']))); ?>円</td>
          <td>
            <a href="<?php print(h(HISTORY_DETAIL_URL) .'?purchased_id=' . $value['purchased_id']);?>" class="btn btn-primary btn-sm btn-block">購入明細</a>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>