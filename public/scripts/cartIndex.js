const cartIndex = {
    init: function () {
        const checkboxesElements = document.querySelectorAll('.form-check-input');
        checkboxesElements.forEach(element => element.addEventListener('change', cartIndex.handleChangeOnsaleStatus));
    },

    handleChangeOnsaleStatus: function (event) {
        const fetchOptions = { method: 'POST'};
        const cartTargetId = event.currentTarget.id;

        fetch('/back/cart/onsale/' + cartTargetId, fetchOptions)
            .then(
                function(response) {
                    if(!response.ok) {
                        document.getElementById(cartTargetId).checked = false;
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
                    }
                }
            );
    }
}

document.addEventListener('DOMContentLoaded', cartIndex.init);