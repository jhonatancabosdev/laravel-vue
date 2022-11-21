let Card = require("card");

let card = new Card({

	form: 'form.jq-checkout-form',

    container: '.card-wrapper',

    width: 300,

    formSelectors: {
        numberInput: 'input#cc_number',
        expiryInput: 'input#cc_expiration',
        nameInput: 'input#cc_name',
        cvcInput: 'input#cc_cvv'
    }
    
});