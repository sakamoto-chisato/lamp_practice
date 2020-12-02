<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  
  <title>商品一覧</title>
  <link rel="stylesheet" href="<?php print(h(STYLESHEET_PATH . 'index.css')); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  

  <div class="container">
    <div class="contents">
      <h1>商品一覧</h1>
      <?php include VIEW_PATH . 'templates/messages.php'; ?>

      <div class="card-deck">
        <div class="row">
        <?php foreach($items as $item){ ?>
          <div class="col-6 item">
            <div class="card h-100 text-center">
              <div class="card-header">
                <?php print(h($item['name'])); ?>
              </div>
              <figure class="card-body">
                <img class="card-img" src="<?php print(h(IMAGE_PATH . $item['image'])); ?>">
                <figcaption>
                  <?php print(h(number_format($item['price']))); ?>円
                  <?php if($item['stock'] > 0){ ?>
                    <form action="index_add_cart.php" method="post">
                      <input type="hidden" name="csrf_token" value="<?php print $token; ?>">
                      <input type="submit" value="カートに追加" class="btn btn-primary btn-block">
                      <input type="hidden" name="item_id" value="<?php print(h($item['item_id'])); ?>">
                    </form>
                  <?php } else { ?>
                    <p class="text-danger">現在売り切れです。</p>
                  <?php } ?>
                </figcaption>
              </figure>
            </div>
          </div>
        <?php } ?>
        </div>
      </div>
      <div class="row justify-content-md-center align-items-center">
        <div><?php print(h($page)); ?>ページ / <?php print(h($total_page)); ?>ページ中</div>
      <?php if ($page>1) { ?>
        <a href="index.php?page=<?php print(h($page-1)); ?>"><div class="btn btn-light">前へ</div></a>
      <?php } ?>
      <?php for($i=1; $i<=$total_page; $i++) {
        if ($i == $page) { ?>
          <div class="btn btn-info"><?php print(h($i)); ?></div>
        <?php } else { ?>
          <a href="index.php?page=<?php print(h($i)); ?>"><div class="btn btn-light"><?php print(h($i)); ?></div></a>
        <?php } ?>
      <?php } ?>
      <?php if ($page < $total_page) { ?>
        <a href="index.php?page=<?php print(h($page+1)); ?>"><div class="btn btn-light">次へ</div></a>
      <?php } ?>
      </div>
    </div>
  </div>
  
</body>
</html>