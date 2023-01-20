<div class="row mt-5 mb-5">
    <div class="col-12">
        <div class="row d-flex justify-content-around">
            <?php echo MenuBox("Carrello", "cart"); ?>
            <?php echo MenuBox("Classe", "class"); ?>
            <?php echo MenuBox("Pickup-Break", "pickup_break"); ?>
            <?php echo MenuBox("Prodotto-Allergeni", "product_allergen"); ?>
            <?php echo MenuBox("Prodotto-Ingredienti", "product_ingredient"); ?>
        </div>
        <div class="row d-flex justify-content-around mt-5">
            <?php echo MenuBox("Prodotto-Offerta", "product_offer"); ?>
            <?php echo MenuBox("Prodotto-Ordini", "product_order"); ?>
            <?php echo MenuBox("Prodotto-Tag", "product_tag"); ?>
            <?php echo MenuBox("Reset", "reset"); ?>
            <?php echo MenuBox("Status", "status"); ?>
        </div>
    </div>
</div>
<div class="row mt-5 mb-5">
    <div class="col-12">
        <div class="row d-flex justify-content-evenly">
            <?php echo MenuBox("Utenti", "user"); ?>
            <?php echo MenuBox("Utenti-Classe", "user_class"); ?>
        </div>
    </div>
</div>
<?php
?>