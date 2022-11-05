const categoryIndex = {
    init: function() {

        const descriptionElementArray = document.querySelectorAll('p.description');
        
        for (const descriptionElement of descriptionElementArray) {
            descriptionElement.addEventListener('click', categoryIndex.handleEnableDescriptionEdit);
        }
    },

    handleEnableDescriptionEdit: function(event) {
        const currentDescriptionParentElt = event.currentTarget.parentElement;
        const descriptionTextareaElt = currentDescriptionParentElt.querySelector('textarea.form-control');
        const alertElt = currentDescriptionParentElt.querySelector('.alert');
        
        event.currentTarget.classList.add('d-none');
        descriptionTextareaElt.classList.remove('d-none');
        alertElt.classList.add('d-none');
        categoryIndex.editDescription(descriptionTextareaElt);
    },

    handleRemoveListeners: function(event) {
        const targetElement = event.currentTarget;
        const currentDescriptionParentElt = targetElement.parentElement;
        const descriptionElement = currentDescriptionParentElt.querySelector('p.description');
        const spinnersElement = currentDescriptionParentElt.querySelector('.spinners');

        targetElement.removeEventListener('blur', categoryIndex.handleRemoveListeners);
        targetElement.removeEventListener('keyup', categoryIndex.handleKeyUp);
        targetElement.classList.add('d-none');
        descriptionElement.classList.remove('d-none');
        spinnersElement.classList.remove('d-none');

        categoryIndex.saveDescription(currentDescriptionParentElt);
    },

    handleKeyUp: function(event) {
        const targetElement = event.currentTarget;
        if (event.key === 'Enter') {
            categoryIndex.handleRemoveListeners(event);
        }
    },

    editDescription: function(textareaElement) {
        textareaElement.focus();
        textareaElement.value = textareaElement.value.trim() === '' ? '' : textareaElement.value;
        textareaElement.addEventListener('blur', categoryIndex.handleRemoveListeners);
        textareaElement.addEventListener('keyup', categoryIndex.handleKeyUp);
    },

    saveDescription: function(descriptionParentElt) {
        const descriptionElement = descriptionParentElt.querySelector('p.description');
        const descriptionTextareaElt = descriptionParentElt.querySelector('textarea.form-control');
        const spinnersElement = descriptionParentElt.querySelector('.spinners');
        const descriptionText = descriptionTextareaElt.value.trim();

        // JSON preparation
        const categoryId = descriptionTextareaElt.dataset.id;
        const data = { 'description': descriptionText };
        const httpHeaders = new Headers();
        httpHeaders.append("Content-Type", "application/json");
        const fetchOptions = {
            method: 'PATCH',
            mode: 'cors',
            cache: 'no-cache',
            headers: httpHeaders,
            body: JSON.stringify(data)
        };

        // Fetch request
        fetch('https://admin.tournichette.fr/back/category/' + categoryId + '/edit-description', fetchOptions)
            .then(
                function(response) {
                    if (response.ok) {
                        return response.json();
                    } else {
                        throw new Error('Erreur lors de la modification de la description : ' + response.statusText + ' (code ' + response.status + ').');
                    }
                }
            )
            .then(
                function(response) {
                    categoryIndex.modifyDOM(descriptionParentElt, response.description);
                }
            )
            .catch(err => categoryIndex.showError(err.message, descriptionParentElt))
    },

    modifyDOM: function(descriptionParentElt, descriptionText) {
        const spinnersElement = descriptionParentElt.querySelector('.spinners');
        const descriptionElement = descriptionParentElt.querySelector('p.description');
        
        if (descriptionText === null) {
            descriptionText = '<em>Pas de desciption. Vous pouvez en ajouter une en cliquant sur ce texte.</em>';
            descriptionElement.classList.remove('w-100', 'bg-opacity-25', 'bg-secondary');
        } else {
            descriptionElement.classList.add('w-100', 'bg-opacity-25', 'bg-secondary');
        }
        descriptionElement.innerHTML = descriptionText;
        spinnersElement.classList.add('d-none');
        
    },

    showError: function(errorMessage, descriptionParentElt) {
        const alertElt = descriptionParentElt.querySelector('.alert');
        const spinnersElement = descriptionParentElt.querySelector('.spinners');

        alertElt.innerText = errorMessage;
        alertElt.classList.remove('d-none');
        spinnersElement.classList.add('d-none');
    }
}

document.addEventListener('DOMContentLoaded', categoryIndex.init);