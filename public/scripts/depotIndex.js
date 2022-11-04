const depotIndex = {
    init: function () {
        const checkboxesElements = document.querySelectorAll('.form-check-input');
        checkboxesElements.forEach(element => element.addEventListener('change', depotIndex.handleChangeAvailableStatus));
    },

    handleChangeAvailableStatus: function (event) {
        const fetchOptions = { method: 'POST'};
        const depotTargetId = event.currentTarget.id;

        fetch('/back/depot/available/' + depotTargetId, fetchOptions)
            .then(
                function(response) {
                    if(!response.ok) {
                        document.getElementById(depotTargetId).checked = false;
                        return false;
                    } else {
                        return response.json();
                    }
                }
            )
            .then(
                function(response) {
                    if (response !== false) {
                        console.log('Disponibilité modifiée avec succès');
                    }
                }
            );
    }
}

document.addEventListener('DOMContentLoaded', depotIndex.init);