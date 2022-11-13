const cartIndex = {
    init: function () {
        const checkboxesElements = document.querySelectorAll('.cart-check');
        checkboxesElements.forEach(element => element.addEventListener('change', cartIndex.handleChangeOnsaleStatus));
    },

    handleChangeOnsaleStatus: function (event) {
        const fetchOptions = { method: 'POST'};
        const cartTarget = event.currentTarget;
        const cartTargetId = cartTarget.dataset.cartId;

        try {
            fetch('/back/cart/onsale/' + cartTargetId, fetchOptions)
                .then(
                    function(response) {
                        if(!response.ok) {
                            cartTarget.checked = false;
                            return false;
                        } else {
                            return response.json();
                        }
                    }
                )
                .then(
                    function(response) {
                        if (response !== false) {
                            console.log('statut de vente modifié avec succès');
                            cartIndex.modifyLabelTarget(cartTargetId);
                        }
                    }
                )
        } catch(err) {
            console.log(err);
        }
    },

    modifyLabelTarget: function(id) {
        const labelTargetElement = document.querySelector('[for="flexSwitch' + id + '"]');
        labelTargetElement.classList.toggle('text-success');
        labelTargetElement.classList.toggle('text-secondary');
        labelTargetElement.classList.toggle('text-opacity-25');
    }
}

document.addEventListener('DOMContentLoaded', cartIndex.init);