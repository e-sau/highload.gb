<?php

include_once "lesson_5.php";

$products = MySqlDB::getInstance()
    ->query('SELECT * FROM goods');

?>
<div class="products">
<?php foreach ($products as $product): ?>
    <div class="product" data-id="<?= $product["id"] ?>"><?= $product["title"] ?></div>
<?php endforeach; ?>
</div>
<div class="modal">
    <div class="modal-col">
        <img src="" alt="" class="product-image">
    </div>
    <div class="modal-col">
        <div class="product-desc"></div>
        <div>
            <span class="product-price"></span> руб.
            <button class="btn-buy" type="button">Купить</button>
        </div>
    </div>
</div>

<script>
    const modal = document.querySelector('.modal');
    const products = document.querySelectorAll('.product');
    products.forEach(el => {
        el.addEventListener('mouseover', e => {
            const id = el.dataset.id;
            let xhr = new XMLHttpRequest();
            xhr.open('GET', '/ajax.php?id=' + id);
            xhr.send();
            xhr.onload = function() {
                if (xhr.status !== 200) {
                    alert(`Ошибка ${xhr.status}: ${xhr.statusText}`);
                } else {
                    const response = JSON.parse(xhr.response);
                    if (~response.id)
                    {
                        modal.querySelector('img').src = '/images/' + response.image;
                        modal.querySelector('.product-desc').textContent = response.description;
                        modal.querySelector('.product-price').textContent = response.price;
                        modal.classList.add('active');
                        console.log(response.data);
                    }
                }
            };
        });
    });

    modal.addEventListener('mouseleave', e => {
        if (modal.classList.contains('active'))
            modal.classList.remove('active');
    });
</script>

<style>
    .products {
        display: flex;
        justify-content: space-around;
    }

    .product {
        position: relative;
        margin: 25px 0;
        padding: 7px 10px;
        border: 1px solid darkgray;
        border-radius: 10px;
        cursor: pointer;
        transition: all .25s;
    }

    .product:hover {
        background: #dedede;
        box-shadow: 2px 2px 3px 0 #b4b4b4;
    }

    .modal {
        position: absolute;
        display: none;
        width: 500px;
        top: 80px;
        left: 50%;
        transform: translateX(-50%);
        border: 1px solid darkgray;
    }

    .modal.active {
        display: flex;
    }

    .modal-col {
        padding: 15px;
    }

    .product-image {
        width: 200px;
    }

    .product-desc {
        margin-bottom: 15px;
        font-size: 16px;
        font-style: italic;
    }

    .product-price {
        color: red;
    }

    .btn-buy {
        margin-left: 10px;
        padding: 5px 7px;
        color: red;
        background: #fff;
        border: 1px solid red;
        border-radius: 5px;
        cursor: pointer;
        transition: all .25s;
    }

    .btn-buy:hover {
        color: #fff;
        background: red;
    }
</style>