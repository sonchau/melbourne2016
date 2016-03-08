
    var PAGE_VALIDATOR;
    var ROW_COUNTER = 1;
    

    var REGO = function () {
        this.Name = '',
        this.Age = '',
        this.Church = '',
        this.Email = '',
        this.Phone = '',
        this.AirBedDiscount = false,
        this.AirportTransfer = false,
        this.Fee = 0,
        this.Registrants = [],
        this.Comments = '',
        this.Reference = '',

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
                && this.Name !== ""
                && this.Phone !== ""
                && this.Email !== "") {

                b = true;

                for (var index in this.Registrants) {
                    person = this.Registrants[index];

                    var isAgeNumeric = isNumeric(person.Age);
                    
                    if (person.Name !== "") {
                        if (!isAgeNumeric) {
                            //console.log("Error: Person age is not a number.")
                            errMsg += "Person " + (parseInt(index) + 1) + " has no age.\n"
                            b = false;
                        } else {
                            b = true;
                        }
                    }

                    if (isAgeNumeric) {
                        if (person.Name == "") {
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

    var REGISTRANT = function (name, age, rel) {
        this.Name = name,
        this.Age = age,
        this.Relation = rel,
        this.DiscountFamily = false,
        this.AirBedDiscount = false,
        this.AirportTransfer = false,
        this.Fee = 0,

        //checks object validiity
        this.isValid = function () {
			return  (isNumeric(this.Age) && $.trim(this.Name) !== "") ;
			
        }
    }

    var REGO_CALCULATOR = {
        type: "calculation",
        family_discount1_amount: 50,
        family_discount2_amount: 100,
        airbed_discount_amount: 20,
        airport_fee: 25,
        calculateFee: function (age) {
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

            return fee

        }
    }



    function removeRow(el) {
        if (ROW_COUNTER == 1 ) {
            return false;
        }
        var div = $(el).parents("div.row:first");
        div.next("div").empty();
        div.empty();
        ROW_COUNTER = ROW_COUNTER - 1;
        collectRegistrantInfo();

    }


    function addMoreRegistrants() {
            
        if (ROW_COUNTER > 9) {
            alert("You can only add 10 registrants at a time.");
            return false;
        }

        for (var x = 1; x < 4; x++) {
            var html = $(".form-inline div.row:first").html();
            html = '<div class="row">' + html + "</div><div>&nbsp;</div>";
            var template = jQuery.validator.format(html);
            $(template(ROW_COUNTER++, "force-show")).insertBefore("#add-more-button-row");

        }


        $(".form-inline input[type=number].age").each(function (index, el) {
            //triggers the age validation for the newly created rows
            PAGE_VALIDATOR.element(el);
        })

        attachEventsToRegistrants();
        collectRegistrantInfo();
        
    }


    function getMainContactInfo() {
        var el = document.getElementById("tAge");
        var age = el.options[el.selectedIndex].text;

        //fee calculation for main contact
        var fee = REGO_CALCULATOR.calculateFee(age);
        var airbed = document.getElementById("airbed00").checked
        var airport = document.getElementById("airport00").checked

        if (airbed) { fee = fee - REGO_CALCULATOR.airbed_discount_amount }
        if (airport) { fee = fee + REGO_CALCULATOR.airport_fee }

        //update the display
        $(".form-horizontal .form-group .line-total").html("$ " + fee.toFixed(2));

        
        //collect other information
        el = document.getElementById("tChurch");
        var church = el.options[el.selectedIndex].text;
        var phone = $("#tPhone").intlTelInput("getNumber"); //document.getElementById("tPhone").value;
        var name = document.getElementById("tFullName").value;
        var email = document.getElementById("tEmail").value;
        var notes = document.getElementById("tNotes").value;

        return {
            email       : htmlEncode($.trim(email)),
            name        : htmlEncode($.trim(name)),
            fee         : fee,
            age         : age,
            airbed      : airbed,
            airport     : airport,
            church      : htmlEncode($.trim(church)),
            phone       : htmlEncode($.trim(phone)),
            notes       : htmlEncode($.trim(notes))
        }


    }


    function collectRegistrantInfo(showJSON) {

        
        //gets the main rego fee
        var groupRego = new REGO();
        var info = getMainContactInfo();

        groupRego.Name            = info.name;
        groupRego.Age             = info.age;
        groupRego.Church          = info.church;
        groupRego.AirBedDiscount  = info.airbed;
        groupRego.AirportTransfer = info.airport;
        groupRego.Fee             = info.fee;
        groupRego.Phone           = info.phone;
        groupRego.Email           = info.email;
        groupRego.Comments        = info.notes;
        groupRego.Reference       = "MELBOURNE2016";    //groupRego.generateReference(10);
        

        var total = 0;
        var person;
        $(".form-inline .row input[type=text].name").each(function (index, el) {
        	person = updateRowFee(el);
        	
        	if (person.isValid()){ //only add to array if person is valid
        		groupRego.Registrants[index] = person;
            	total = total + person.Fee;
        	}
            
        })

        total = total + groupRego.Fee;

        document.getElementById("TotalAmount").value = total;

        showJSON = 0;
        if (showJSON) {
            //alert(JSON.stringify(aa))
            $("#json").html(JSON.stringify(groupRego))
        }

        return groupRego;
    }


    function updateRowFee(el) {

        var groups = new REGISTRANT();


        var age = "";
        var name = "";
        var fee = 0;

        var div = $(el).parents("div.row:first");
            
        $(div).find("input[type=text].name").each(function () {
            name = htmlEncode($.trim($(this).val()));
        })
        $(div).find("input[type=number].age").each(function () {
            age = parseInt($(this).val())
        })


        if (name !== "" && isNaN(age) == false) {
            fee = REGO_CALCULATOR.calculateFee(age);
        }
            
        if (fee > 0) {
            // only calculate discount if age fee calculation is > 0

            //we see if any selection of the family discount
            $(div).find("select.family-discount").each(function (index, e) {
                switch (e.selectedIndex) {
                    case 1:
                        fee = fee - REGO_CALCULATOR.family_discount1_amount
                        break;
                    case 2:
                        fee = fee - REGO_CALCULATOR.family_discount2_amount
                        break;
                    default:
                }
                groups.DiscountFamily = $(e).val()
            });


            //find the airbed discount
            $(div).find("input[type=checkbox].discount-airbed:checked").each(function (index, el) {
                fee = fee - REGO_CALCULATOR.airbed_discount_amount
                groups.AirBedDiscount = el.checked;
            });


            //find the airport transfer
            $(div).find("input[type=checkbox].airport-transfer:checked").each(function (index, el) {
                fee = fee + REGO_CALCULATOR.airport_fee;
                groups.AirportTransfer = el.checked;
            });

        }

        if (fee < 0) { fee = 0 }


        //display the fee
        $(div).find(".line-total").each(function () {
            $(this).html("$ " + fee.toFixed(2))//.fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
                
        });



        groups.Age = age;
        groups.Name = $(div).find("input[type=text].name").val();
        groups.Fee = fee;
        groups.Relation = $(div).find("select.relation:first").val()

        return groups


    }


    // updates the data template with the rego data for summary
    function updateSummary(htmlTemplate) {
        var regoInfo = collectRegistrantInfo();

        htmlTemplate = jQuery.validator.format(htmlTemplate);
        //find out if there are any extra registrants for the rego
        var registrants = "";
        var paymentSummary = "";

        var html = "<tr><td>{NAME}</td><td>{AGE}</td><td>{RELATION}</td><td>{FAMILY}</td><td>{AIRBED}</td><td>{AIRPORT}</td><td>${FEE}</td></tr>";
        var htmlShort = "<tr><td>{COUNTER}.</td><td>{NAME}</td><td>{AGE}</td><td>${FEE}</td></tr>";

        //add the main contact to the payment summary
        paymentSummary = htmlShort.replace("{NAME}",        regoInfo.Name)
                                    .replace("{AGE}",       regoInfo.Age)
                                    .replace("{COUNTER}",   "1")
                                    .replace("{FEE}",       regoInfo.Fee);

        
        var person;
        var personCounter=0;
        for (var index in regoInfo.Registrants) {
            person = regoInfo.Registrants[index];

            if (person.isValid()) {
                personCounter += 1
                registrants += html.replace("{NAME}",       person.Name)
                                    .replace("{AGE}",       person.Age)
                                    .replace("{RELATION}",  person.Relation)
                                    .replace("{FAMILY}",    person.DiscountFamily)
                                    .replace("{AIRBED}",    toYesNo(person.AirBedDiscount))
                                    .replace("{AIRPORT}",   toYesNo(person.AirportTransfer))
                                    .replace("{FEE}",       person.Fee);


                paymentSummary += htmlShort.replace("{NAME}",       person.Name)
                                            .replace("{AGE}",       person.Age)
                                            .replace("{COUNTER}",   (parseInt(personCounter) + 2))
                                            .replace("{FEE}",       person.Fee);

            }
    
        }

        //the extra registrants display attr
        var display = (registrants == "" ? "hidden" : "");





        $("#summary-content").html(
            $(htmlTemplate(
                regoInfo.Reference,
                regoInfo.Name,
                regoInfo.Age,
                regoInfo.Email,
                regoInfo.Phone,
                toYesNo(regoInfo.AirBedDiscount),
                toYesNo(regoInfo.AirportTransfer),
                regoInfo.Church,
                regoInfo.Fee,
                regoInfo.Comments,
                display,
                registrants,
                regoInfo.sumTotalFees(true),
                paymentSummary,
                regoInfo.sumTotalFees()
                )
            ).html()

        );

        return true;

    }


    //validator hookup for age and name to be dependant on each other
    $.validator.addMethod("age", function (value, element) { 
        //alert(value)
        if ($.trim(value) == "") {
            return true;
        }
        return !this.optional(element) && !this.optional($(element).parent().prev().children("input[type=text].name")[0]);
    }, "Name &amp; Age required 1");

   
    $(function () {

        attachEventsToMainContact();
        attachEventsToRegistrants();
        addCountriesToPhone();

        PAGE_VALIDATOR =  $("#rego-form").validate({
            debug: true,
            focusInvalid: false,
            errorClass: "validate-error",
            submitHandler: function (form) {

                var groupRego = collectRegistrantInfo();
                var result = groupRego.validate(); //does the main contact validation 
                
                if (!result.isValid) {
                    alert(result.errMsg)
                    return false;
                } else {
                    //alert("get summary")
                    getSummary();
                }
                
            },
            invalidHandler: function (form, validator) {

                if (!validator.numberOfInvalids())
                    return;

                    
                var top = $(validator.errorList[0].element).offset().top;
                if (top > 50) { top = top - 60 }
                if (top < 0) { top = 0}

                $('html, body').animate({
                    scrollTop: top
                }, 750);
            }


        });


    });


    function attachEventsToRegistrants() {
        $("div.form-inline input, div.form-inline select.family-discount").each(function (index, el) {
            $(el).bind("change", function () {

                collectRegistrantInfo()
            })
        });
    }


    function attachEventsToMainContact() {
        //attach to main contact
        $(".form-horizontal input, .form-horizontal select").each(function (index, el) {
            $(el).bind("change", function () {

                collectRegistrantInfo()
            })
        });
   
    }


    function addCountriesToPhone() {
        //https://github.com/jackocnr/intl-tel-input#browser-compatibility
        $("#tPhone").intlTelInput({
            // allowDropdown: false,
            // autoHideDialCode: false,
            // autoPlaceholder: false,
            // dropdownContainer: "body",
            excludeCountries: ["do"],
            // geoIpLookup: function(callback) {
            //   $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
            //     var countryCode = (resp && resp.country) ? resp.country : "";
            //     callback(countryCode);
            //   });
            // },
            // initialCountry: "auto",
            // nationalMode: false,
            // numberType: "MOBILE",
            // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
            preferredCountries: ['au', 'nz', 'us', 'ca'],
            // separateDialCode: true,
            utilsScript: "/js/utils.js"
        });

        //telInput.intlTelInput("isValidNumber")
        //$("#tPhone").intlTelInput("getNumber")

        PHONE_VALIDATOR.bindPhone();

    }



    var PHONE_VALIDATOR = {
        telInput:   $("#tPhone"),
        errorMsg:   $("#phone-error-msg"),
        validMsg:   $("#phone-valid-msg"),
        reset: function () {
            PHONE_VALIDATOR.errorMsg.addClass("hidden");
            PHONE_VALIDATOR.validMsg.addClass("hidden");
        },

        bindPhone: function(){
            
            // on blur: validate
            this.telInput.blur(function () {
                PHONE_VALIDATOR.reset();
                if ($.trim($(this).val())) {
                    if ($(this).intlTelInput("isValidNumber")) {
                        PHONE_VALIDATOR.validMsg.removeClass("hidden");
                    } else {
                        PHONE_VALIDATOR.errorMsg.removeClass("hidden");
                    }
                }
            });

            // on keyup / change flag: reset
            this.telInput.on("keyup change", PHONE_VALIDATOR.reset);

        }


    }



    function toYesNo(v) {

        if (v == "1" || v == "true") {
            return "Yes"
        } else {
            return "No"
        }

    }


    function addCommas(nStr) {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }


    function getSummary() {


        $.ajax({
            url: 'summary_template.html',
            type: 'GET',
            dataType: 'html',
            data: { cache: Math.random() },
        })
        .success(function (data) {
            //document.getElementById("summary-content").innerHTML = data;
            updateSummary(data);
            swapRegoSummary();
        })
        .done(function (data) {
            //alert(data)
            //document.getElementById("summary-content").innerHTML = data;

        })
        .fail(function () {
            console.log("error");
            alert("error")
        })
        .always(function () {
            console.log("complete");

        });


    }

    function swapRegoSummary() {
        $("#rego-form").toggle();
        $("#rego-summary").fadeToggle();

        $('html, body').animate({
            scrollTop: $('#fee-structure-end').offset().top
        }, 500);

    }


    function htmlEncode(value) {
        //create a in-memory div, set it's inner text(which jQuery automatically encodes)
        //then grab the encoded contents back out.  The div never exists on the page.
        return $('<div/>').text(value).html();
    }

    function htmlDecode(value) {
        return $('<div/>').html(value).text();
    }

    function isNumeric(n) {
      return !isNaN(parseFloat(n)) && isFinite(n);
    }    