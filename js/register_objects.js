    var REGO = function () {
        this.Firstname       = '',
        this.Surname         = '',
        this.Age             = '',
        this.Role            = '',
        this.Gender          = '',
        this.Church          = '',
        this.Email           = '',
        this.Phone           = '',
        this.Airbed          = false,
        this.AirportTransfer = false,
        this.Fee             = 0,
        this.Registrants     = [],
        this.Comments        = '',
        this.Reference       = '',
        this.Cancelled       = false,
        this.DiscountAmount  = 0,


        this.Name = function(){
            var n = this.Firstname + ' ' + this.Surname; 
            return n.trim();
        }

        this.generateReference = function (length) {
            var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
            var pass = "";
            for (var x = 0; x < length; x++) {
                var i = Math.floor(Math.random() * chars.length);
                pass += chars.charAt(i);
            }
            return pass;
        }

        this.sumTotalFees = function (additionalsOnly) {
            if (typeof additionalsOnly === "undefined") { additionalsOnly = false } //makes the param optional

            var total = (additionalsOnly === true ? 0 : this.Fee);

            var person;
            for (var index in this.Registrants) {
                person = this.Registrants[index];
                if (person.isValid()){
                    total += person.Fee;
                }
    
            }

            return total;
        }


        this.validate = function () {

            var errMsg = "";
            var b = false;

            if (!isNumeric(this.Age)) {
                errMsg = "Error: Main contact age is not a number.\n";

                return {
                    isValid: b,
                    errMsg: errMsg
                }

            }
            

            
            //make sure there is a main contact.
            if (this.Age >= 16
                && this.Name()      !== ""
                && this.Phone       !== ""
                && this.Email       !== "") {

                b = true;

                for (var index in this.Registrants) {
                    person = this.Registrants[index];

                    var isAgeNumeric = isNumeric(person.Age);
                    
                    if (person.Name() !== "") {
                        if (!isAgeNumeric) {
                            //console.log("Error: Person age is not a number.")
                            errMsg += "Person " + (parseInt(index) + 1) + " has no age.\n"
                            b = false;
                        } else {
                            b = true;
                        }
                    }

                    if (isAgeNumeric) {
                        if (person.Name() == "") {
                            //console.log("Error: Person has age but no Name.")
                            errMsg += "Person " + (parseInt(index) + 1) + " has no Name.\n"
                            b = false;
                        } else {
                            b = true;
                        }
                    }

                }




            } else {
                errMsg += "Please ensure you have the main contact details correctly filled out.\n"
                b = false
            }

            return {
                isValid: b,
                errMsg: errMsg
            }
        }

    }

    var REGISTRANT = function (name, surname, age, rel) {
        this.Firstname       = name,
        this.Surname         = surname,
        this.Age             = age,
        this.Role            = '',
        this.Gender          = '',
        this.Relation        = rel,
        this.DiscountFamily  = false,
        this.Airbed          = false,
        this.AirportTransfer = false,
        this.Fee             = 0,
        this.Cancelled       = false,
        this.DiscountAmount  = 0,


        this.Name = function(){
            var n = this.Firstname + ' ' + this.Surname; 
            return n.trim();
        }

        //checks object validiity
        this.isValid = function () {
            return  (isNumeric(this.Age) && $.trim(this.Name()) !== "") ;
            
        }
    }

    var REGO_CALCULATOR = {
        type: "calculation",
        family_discount1_amount: 50,
        family_discount2_amount: 100,
        airbed_discount_amount: 20,
        airport_fee: 25,
        early_bird_discount_amount: 40,

        calculateFee: function (age) { //does fee calculate on age
            var fee = 0;
            switch (true) {
                case (age <= 5):
                    fee = 50
                    break;
                case (age > 5 && age <= 12):
                    fee = 350
                    break;
                case (age > 12 && age < 65):
                    fee = 440
                    break;
                case (age >= 65):
                    fee = 390
                    break;
                default:

            }

            
            fee = this.calculateEarlyBirdDiscount(fee);

            return fee;

        },
        calculateFee2: function (age,airbed,airport,family_discount){ //does fee calculation on all aspects

            var fee = 0;


            if (isNaN(age) == false) {
                fee = this.calculateFee(age);
            }


            // only calculate discount if age fee calculation is > 0
            if (fee > 0) {
                    //we see if any selection of the family discount
                    switch (family_discount) {
                        case 1:
                            if (age < 6){
                                fee = fee - this.family_discount1_amount
                            }
                            break;
                        case 2:
                            fee = fee - this.family_discount2_amount
                            break;
                        default:
                    }

            }


            if (fee > 0 && airbed){
                fee = fee - this.airbed_discount_amount
            }

            if (fee > 0 && airport){
                fee = fee + this.airport_fee;
            }            
            
            //no need to recalculate early bird fee as its already called in: this.calculateFee(age);
            //fee = this.calculateEarlyBirdDiscount(fee);

            if (fee < 0) { fee = 0 }


            return fee;

        },
        calculateEarlyBirdDiscount: function (fee) { //does fee calculate on age
            if (isNaN(fee)){
                return fee;
            }


            var nowDate = new Date();
            var earlybirdDate = new Date(2016,09,30);
            if (nowDate < earlybirdDate){
                fee = fee - REGO_CALCULATOR.early_bird_discount_amount;
            }

            return fee;

        }






        //end class
    }



