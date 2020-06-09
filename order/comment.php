<?php

require_once (__DIR__ . '/../vendor/autoload.php');

$userId = (int) $_GET['user'];
$orderId = (int) $_GET['order'];
if (!$userId || !$orderId) header("Location: /order/");

$hash = (new \App\models\User())->selectById($userId)['hash'];

?>

<link rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">

<div class="container">
    <h3>КОММЕНТАРИЙ</h3>
    <form action="/" method="post" name="comment">
        <input type="hidden" name="event" value="new_comment">
        <input type="hidden" name="user_id" value="<?= $userId ?>">
        <input type="hidden" name="order_id" value="<?= $orderId ?>">
        <div class="form-group row">
            <div class="col-sm-12">
                <input type="text" class="form-control" id="name" name="message">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-10">
                <button type="submit" class="btn btn-primary">Отправить</button>
            </div>
        </div>
    </form>
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

    .overlay {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: rgba(0,0,0,.3);
        z-index: 999;
    }

    .modal-text {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 320px;
        padding: 20px 30px;
        background: #fff;
        text-align: center;
        font-size: 18px;
        transform: translate(-50%, -50%);
        z-index: 1000;
    }
</style>

<script>
    const form = document.querySelector('form');
    form.addEventListener('submit', e => {
        e.preventDefault();
        let formData = new FormData(form);
        let xhr = new XMLHttpRequest();
        xhr.open('POST', '/order/api/Api.php');
        xhr.send(formData);
        xhr.onload = function () {
            if (xhr.response) {
                const response = JSON.parse(xhr.response);
                console.log(response);
                if (response.OK) {
                    const overlay = document.createElement('div');
                    overlay.classList.add('overlay');
                    const modal = document.createElement('div');
                    modal.classList.add('modal-text');
                    modal.textContent = response.OK;
                    document.body.append(overlay, modal);
                    setTimeout(() => {
                        window.location.href = "/order/account.php?h=<?= $hash ?>";
                    }, 2000);
                }
            }
        };
    });
</script>