const harvestSwitch = {

    pdfButtonOriginalValue: null,

    init: function() {

        const packRadioElements = document.querySelectorAll('input[name="pack"]');
        
        harvestSwitch.pdfButtonOriginalValue = document.querySelector('#print-pdf').href;

        for (const currentRadioElement of packRadioElements) {
            currentRadioElement.addEventListener('change', harvestSwitch.handleChange);
        }
    },

    handleChange: function(event) {
        const showPack = event.currentTarget.value;
        harvestSwitch.modifyDOM(showPack);
    },

    modifyDOM: function(showPack) {
        const productsTotalElements = document.querySelectorAll('.products-by-total');
        const productsPackElement = document.querySelectorAll('.products-by-pack');
        const pdfButtonElement = document.querySelector('#print-pdf');

        if (showPack == 'true') {
            for (const currentElement of productsTotalElements) {
                currentElement.classList.add('d-none');
            }

            for (const currentElement of productsPackElement) {
                currentElement.classList.remove('d-none');
            }

            pdfButtonElement.href = harvestSwitch.pdfButtonOriginalValue + '&sort=pack';
            
        } else if (showPack == 'false') {
            for (const currentElement of productsTotalElements) {
                currentElement.classList.remove('d-none');
            }

            for (const currentElement of productsPackElement) {
                currentElement.classList.add('d-none');
            }

            pdfButtonElement.href = harvestSwitch.pdfButtonOriginalValue;
        }
    }
}

document.addEventListener('DOMContentLoaded', harvestSwitch.init);