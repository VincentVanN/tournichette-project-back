const cart = {
    init: function () {
        console.log('Cart script initialized');
        cart.createProductElement();
        const buttonAddProductElement = document.querySelector('#product-add');
        buttonAddProductElement.addEventListener('click', cart.handleAddProductElement);
    },

    handleAddProductElement: function(event) {
        cart.createProductElement();
    },

    createProductElement: function () {
        const templateProductElement = document.querySelector('#select-product-template').content.cloneNode(true);
        const newProductElement = templateProductElement.querySelector('.input-group');
        const buttonAddProductElement = document.querySelector('#product-add')

        buttonAddProductElement.parentNode.insertBefore(newProductElement, buttonAddProductElement);

        // console.log(divMb3Element);

    }
}

document.addEventListener('DOMContentLoaded', cart.init);