<?php
if (is_page('allergen')) {
    get_template_part('templates/page', 'allergen');
} elseif (is_page('cart')) {
    get_template_part('templates/page', 'cart');
} elseif (is_page('product')) {
    get_template_part('templates/page', 'product');
} elseif (is_page('user')) {
    get_template_part('templates/page', 'user');
} else {
    echo "error: unknown page type";
}

/*
[
"allergen",
"break",
"cart",
"class",
"favourite",
"ingredient",
"nutritional_value",
"offer",
"order",
"pickup",
"pickup_break",
"product",
"product_allergen",
"product_ingredient",
"product_offer",
"product_order",
"product_tag",
"reset",
"status",
"tag",
"user",
"user_class"
]
*/
?>