const cartIndex = {
    init: function () {
        console.log('Cart-index initialized');
        checkboxesElements = document.querySelectorAll('.form-check-input');
        checkboxesElements.forEach(element => element.addEventListener('change', cartIndex.handleChangeOnsaleStatus));
    },

    handleChangeOnsaleStatus: function (event) {
        // console.log(event.currentTarget.id);
        const fetchOptions = { method: 'POST'};
        cartTargetId = event.currentTarget.id;

        fetch('/back/cart/onsale/' + event.currentTarget.id, fetchOptions)
            .then(
                function(response) {
                    if(!response.ok) {
                        // throw new Error(response.status + ' ' + cartTargetId);
                        document.getElementById(cartTargetId).checked = false;
                        return false;
                    } else {
                        return response.json();
                    }
                    ;
                }
            )
            .then(
                function(response) {
                    if (response !== false) {
                        console.log('statut de vente modifié avec succès');
                    }
                }
            )
            // .catch(function(error) {
            //     console.log(error.message);
            // });
    }
}

document.addEventListener('DOMContentLoaded', cartIndex.init);