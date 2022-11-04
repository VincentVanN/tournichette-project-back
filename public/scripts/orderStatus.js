const orderStatus = {
    init: function() {
        
        const paidStatusElements = document.querySelectorAll('a.bi-cash-coin');
        const deliveredStatusElement = document.querySelectorAll('a.bi-clipboard2-check');
        
        paidStatusElements.forEach(element => element.addEventListener('click', orderStatus.handlePaidStatus));
        deliveredStatusElement.forEach(element => element.addEventListener('click', orderStatus.handleDeliveredStatus));
    },

    handlePaidStatus: function(event) {
        event.preventDefault();
        const orderId = event.currentTarget.dataset.payment;
        const fetchOptions = { method: 'POST'};
        
        fetch('/back/orders/validate/' + orderId, fetchOptions)
            .then(
                function(response) {
                    if(response.ok) {
                        return response.json();
                    } else {
                        console.log(response.status + ' ' + response.statusText);
                        return false
                    }
                }
            )
            .then(
                function(response) {
                    if(response !== false) {
                        orderStatus.modifyPaidElement(response.orderId, response.paidAt);
                    }
                }
            )
    },

    handleDeliveredStatus: function(event) {
        event.preventDefault();
        const orderId = event.currentTarget.dataset.delivery;
        const fetchOptions = { method: 'POST'};
        
        fetch('/back/orders/delivered/' + orderId, fetchOptions)
            .then(
                function(response) {
                    if(response.ok) {
                        return response.json();
                    } else {
                        console.log(response.status + ' ' + response.statusText);
                        return false
                    }
                }
            )
            .then(
                function(response) {
                    if(response !== false) {
                        orderStatus.modifyDeliveredElement(response.orderId, response.deliveredAt);
                    }
                }
            )
    },

    modifyPaidElement: function(elementId, datePayment) {
        const targetElement = document.querySelector('[data-payment="' + elementId + '"]');
        const parentTargetElement = targetElement.parentNode;
        const nextParentTargetElementSibling = parentTargetElement.nextElementSibling;

        parentTargetElement.removeChild(targetElement);
        nextParentTargetElementSibling.textContent = datePayment;

        const targetElementReplacement = document.createElement('i');
        targetElementReplacement.classList.add('bi', 'bi-cash-coin', 'text-success');
        parentTargetElement.appendChild(targetElementReplacement);

        orderStatus.createDeliveredElement(nextParentTargetElementSibling, elementId);
    },

    modifyDeliveredElement: function(elementId, dateDelivered) {
        const targetElement = document.querySelector('[data-delivery="' + elementId + '"]');
        const parentTargetElement = targetElement.parentNode;
        const nextParentTargetElementSibling = parentTargetElement.nextElementSibling;

        parentTargetElement.removeChild(targetElement);
        nextParentTargetElementSibling.textContent = dateDelivered;

        const targetElementReplacement = document.createElement('i');
        targetElementReplacement.classList.add('bi', 'bi-clipboard2-check', 'text-success');
        parentTargetElement.appendChild(targetElementReplacement);
    },

    createDeliveredElement:  function(datePaymentElement, elementId) {
        const buttonDeliveredElement = document.createElement('a');
        buttonDeliveredElement.href = "#";
        buttonDeliveredElement.classList.add('btn', 'btn-outline-danger', 'bi', 'bi-clipboard2-check', 'text-danger');
        buttonDeliveredElement.dataset.delivery = elementId;

        buttonDeliveredElement.addEventListener('click', orderStatus.handleDeliveredStatus);

        datePaymentElement.nextElementSibling.appendChild(buttonDeliveredElement);
    }
}

document.addEventListener('DOMContentLoaded', orderStatus.init);