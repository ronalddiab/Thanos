function list_addtocart(product_in_id)
{
    document.getElementById('product_id').value = document.getElementById('lproduct_id' + product_in_id).value;
    document.getElementById('price').value = document.getElementById('lprice' + product_in_id).value;
    document.getElementById('pname').value = document.getElementById('lpname' + product_in_id).value;
    document.getElementById('discount_price').value = document.getElementById('ldiscount_price' + product_in_id).value;
    document.getElementById('currency_code').value = document.getElementById('lcurrency_code' + product_in_id).value;
    document.getElementById('slug_url').value = document.getElementById('lslug_url' + product_in_id).value;
    document.getElementById('product_image').value = document.getElementById('lproduct_image' + product_in_id).value;

    //document.shopping_products.action = 'shoppingcart/addtocart';

    document.shopping_products.submit();
}

function product_addtocart(product_in_id, formname)
{
    if (formname == 'shopping_product')
    {
        document.shopping_products.submit();
        return false;
    }

    if (formname == 'shopping_featureproduct')
    {
        document.shopping_featureproduct.product_id.value = document.getElementById('lproduct_id' + product_in_id).value;
        document.shopping_featureproduct.price.value = document.getElementById('lprice' + product_in_id).value;
        document.shopping_featureproduct.pname.value = document.getElementById('lpname' + product_in_id).value;
        document.shopping_featureproduct.discount_price.value = document.getElementById('ldiscount_price' + product_in_id).value;
        document.shopping_featureproduct.currency_code.value = document.getElementById('lcurrency_code' + product_in_id).value;
        document.shopping_featureproduct.slug_url.value = document.getElementById('lslug_url' + product_in_id).value;
        document.shopping_featureproduct.product_image.value = document.getElementById('lproduct_image' + product_in_id).value;
        document.shopping_featureproduct.submit();
        return false;
    }
}

