const salesSwitch = {
    init: function() {
        const salesSwitchElement = document.getElementById('salesSwitchStatus');
        salesSwitchElement.addEventListener('change', salesSwitch.handleEnablingSales);
    },

    handleEnablingSales: async function(event) {
        event.preventDefault();
        const salesSwitchChecked = event.currentTarget.checked == true ? 'enable' : 'disable';

        try {
            fetch('/back/sales/' + salesSwitchChecked, { 
                method: 'GET'
            })
            .then(salesSwitch.handleErrorsPromise)
            .then(salesSwitch.modifySalesSwitchText(salesSwitchChecked))
            .catch(err => console.log(err))
        } catch(err){
            console.log(err);
        }
    },

    handleErrorsPromise: function(response) {
        if (response.ok) {
            return response
        }
        throw new Error(response.statusText);
    },

    modifySalesSwitchText: function(salesSwitchStatus) {
        const salesOpenTxtElement = document.getElementById('salesStatusOpen');
        const salesCloseTxtElement = document.getElementById('salesStatusClose');
        const classHideSalesTextElement = 'd-none';
        
        switch (salesSwitchStatus) {
            case 'enable':
                salesCloseTxtElement.classList.add(classHideSalesTextElement);
                salesOpenTxtElement.classList.remove(classHideSalesTextElement);
                break;
            case 'disable':
                salesCloseTxtElement.classList.remove(classHideSalesTextElement);
                salesOpenTxtElement.classList.add(classHideSalesTextElement);
                break;
        }
    }
}

document.addEventListener('DOMContentLoaded', salesSwitch.init);