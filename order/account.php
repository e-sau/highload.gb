<?php

require_once (__DIR__ . '/../vendor/autoload.php');

$orders = [];
$hash = (int) $_GET['h'];

if ($hash) {
    $user = (new \App\models\User())->selectByHash($hash);
    $orders = (new \App\models\Order())->selectByUserId($user['id']);
}

?>

<link rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">

<div class="container">
    <h3>ЛИЧНЫЙ КАБИНЕТ</h3>
    <div class="orders">
        <?php foreach ($orders as $order): ?>
        <div class="card">
            <div class="card-body order-item" data-order="<?= $order['id'] ?>">
                <p>Заказ №<?= $order['id'] ?></p>
                <p>Параметры: <?= $order['data'] ?></p>
                <p>Доставлен: <span class="order-delivery"><?= $order['is_delivered'] ? "да" : "нет"; ?></span></p>
                <button type="button" class="btn btn-info order-info">Обновить инфо</button>
                <a class="btn btn-outline-success order-comment-btn<?= $order['is_delivered'] ? " order-comment-btn-active" : ""; ?>"
                   href="/order/comment.php?user=<?= $user['id'] ?>&order=<?= $order['id'] ?>">Оставить комментарий</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .container {
        margin: 20px auto 0;
        width: 640px;
    }

    h3 {
        margin-bottom: 25px;
        padding-bottom: 7px;
        text-align: center;
        font-size: 24px;
        color: darkslateblue;
        letter-spacing: 1em;
        border-bottom: 1px solid darkslateblue;
    }

    .order-comment-btn {
        visibility: hidden;
    }

    .order-comment-btn-active {
        visibility: visible;
    }
</style>

<script>
    const orderInfoBtns = document.querySelectorAll('.order-info');
    const form = document.querySelector('form');
    orderInfoBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            let xhr = new XMLHttpRequest();
            xhr.open('POST', '/order/api/Api.php');
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            const parent = btn.parentElement;
            const orderId = parent.dataset.order;
            xhr.send('data=' + JSON.stringify({method: 'isOrderDelivered', orderId}));
            xhr.onload = function () {
                console.log('Order info request');
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.response);
                    console.log(response);
                    if (response.is_delivered === true) {
                        parent.querySelector('.order-delivery').textContent = "да";
                        parent.querySelector('.order-comment-btn').classList.add('order-comment-btn-active');
                    }
                }
            };
        });
    })
</script>
