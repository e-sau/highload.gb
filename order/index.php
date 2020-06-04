<?php

echo "<pre>";
//print_r($_POST);
echo "</pre>";

$sandwiches = [
    "hamburger" => "Гамбургер",
    "cheeseburger" => "Чизбургер",
    "shaurma" => "Шаурма",
    "danar" => "Данар",
];

$additions = [
    "sauce" => "Кетчуп",
    "mayonnaise" => "Майонез",
    "onion" => "Лук"
];

?>

<link rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">

<div class="container">
    <h3>ЗАКАЗ</h3>
    <form action="/" method="post" name="order">
        <input type="hidden" name="event" value="new_order">
        <div class="form-group row">
            <div class="col-sm-2">Сэндвич:</div>
            <div class="col-sm-10">
                <select class="form-control" id="sandwich" name="sandwich">
                    <?php foreach ($sandwiches as $key => $title): ?>
                        <option value="<?= $key ?>"><?= $title ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2">Добавки:</div>
            <div class="col-sm-10">
                <?php foreach ($additions as $key => $title): ?>
                    <div class="form-check">
                        <input type="checkbox" id="<?= $key ?>" name="additions[]" value="<?= $key ?>">
                        <label class="form-check-label" for="<?= $key ?>">
                            <?= $title ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="form-group row">
            <label for="name" class="col-sm-2 col-form-label">Имя:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="name" name="name">
            </div>
        </div>
        <div class="form-group row">
            <label for="phone" class="col-sm-2 col-form-label">Телефон:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="phone" name="phone">
            </div>
        </div>
        <div class="form-group row">
            <label for="address" class="col-sm-2 col-form-label">Адрес доставки:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="address" name="address">
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
            console.log('Form loaded');
            if (xhr.response) {
                const response = JSON.parse(xhr.response);
                console.log(response);
            }
        };
    });
</script>