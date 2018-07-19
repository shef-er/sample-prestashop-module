<div id="test_module_block" class="block">
    <h4>Добро пожаловать!</h4>
    <div class="test_module_content">
        <p>
            Сейчас на сайте {if isset($test_module_product_count) && $test_module_product_count}{$test_module_product_count}{/if}
            товаров в дипазоне от {if isset($test_module_price_from) && $test_module_price_from}{$test_module_price_from}{/if} до
            {if isset($test_module_price_to) && $test_module_price_to}{$test_module_price_to}{/if} руб.
        </p>
    </div>
</div>

<script>
    $(document).ready(function() {
        $.fancybox("#test_module_block");
    });
</script>