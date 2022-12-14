const productIndex = {
    init: function () {
        const checkboxesElements = document.querySelectorAll('.product-check');
        checkboxesElements.forEach(element => element.addEventListener('change', productIndex.handleChangeOnsaleStatus));
    },

    handleChangeOnsaleStatus: function (event) {
        const fetchOptions = { method: 'POST'};
        const productTarget = event.currentTarget;
        const productTargetId = productTarget.dataset.productId;

        try {
        fetch('/back/product/onsale/' + productTargetId, fetchOptions)
            .then(
                function(response) {
                    if(!response.ok) {
                        productTarget.checked = false;
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
                        productIndex.modifyLabelTarget(productTargetId);
                    }
                }
            );
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

document.addEventListener('DOMContentLoaded', productIndex.init);