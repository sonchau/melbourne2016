

    var ROW_COUNTER = 1;

    var Registrant = function (name, age, rel) {
        this.Name = name,
        this.Age = age,
        this.Relation = rel,
        this.DiscountFamily = 0,
        this.DiscountAirBed = 0,
        this.DiscountOverseas = 0,
        this.AirportTransfer = 0,
        this.Fee = 0
    }


    function removeRow(el) {
        if (ROW_COUNTER == 1 ) {
            return false;
        }
        var div = $(el).parents("div.row:first");
        div.next("div").empty();
        div.empty();
        ROW_COUNTER = ROW_COUNTER - 1;
        collectRegistrantInfo()

    }

    function addMoreRegistrants2() {
            
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

        attachEventsToRegistrants();
        collectRegistrantInfo();


    }

    function addMoreRegistrants() {

        addMoreRegistrants2()
        return false;


        for (var x = 1; x < 4; x++){
            var cloned = $(".form-inline div.row:first").clone().append("<div>&nbsp;</div>");

            cloned.find("input").each(function (index, el) {
                el.value = "";
            });
            cloned.find("select").each(function (index, el) {
                el.selectedIndex = 0
            });


                
            ROW_COUNTER = ROW_COUNTER + 1;

            cloned.find("input[type=checkbox]").each(function (index, el) {
                el.checked = false;
                el.id = index.toString() + "cb_" + ROW_COUNTER.toString();
                $(el).next("label").prop("for",index.toString() + "cb_" + ROW_COUNTER.toString());

            });

            cloned.insertBefore("#add-more-button-row");
        }

        attachEventsToRegistrants();
        collectRegistrantInfo();

    }


    function getMainContactFee() {
        var el = document.getElementById("tAge");
        var fee = REGO.calculateFee(el.options[el.selectedIndex].text);

        if (document.getElementById("airbed00").checked) { fee = fee - 20 }
        if (document.getElementById("airport00").checked) { fee = fee + 25 }

        $(".form-horizontal .form-group .line-total").html("$ " + fee.toFixed(2));

        return fee;
    }

    function collectRegistrantInfo(showJSON) {

        aa = [];


        var total = 0;
        $(".form-inline .row input[type=text].name").each(function (index, el) {
            aa[index] = updateRowFee(el)
            total = total + aa[index].Fee;
        })

        total = total + getMainContactFee()

        document.getElementById("TotalAmount").value = total;

        if (showJSON) {
            alert(JSON.stringify(aa))
        }
    }


    var REGO = {
        type: "calculation",
        calculateFee: function (age) {
            var fee = 0;
            switch (true) {
                case (age <= 5):
                    fee = 50
                    break;
                case (age > 5 && age <= 12):
                    fee = 350
                    break;
                case (age > 12 && age < 64):
                    fee = 440
                    break;
                case (age >= 64):
                    fee = 390
                    break;
                default:

            }

            return fee

        }
    }


    function updateRowFee(el) {

        var regoInfo = new Registrant();


        var age = -1;
        var name = ""
        var fee = 0

        var div = $(el).parents("div.row:first");
            
        $(div).find("input[type=text].name").each(function () {
            name = $.trim($(this).val());
        })
        $(div).find("input[type=number].age").each(function () {
            age = parseInt($(this).val())
        })


        if (name !== "") {
            fee = REGO.calculateFee(age);
        }
            
        if (fee > 0) {
            // only calculate discount if age fee calculation is > 0

            //we see if any selection of the family discount
            $(div).find("select.family-discount").each(function (index, e) {
                switch (e.selectedIndex) {
                    case 1:
                        fee = fee - 50
                        break;
                    case 2:
                        fee = fee - 100
                        break;
                    default:
                }
                regoInfo.DiscountFamily = $(e).val()
            });


            //find the airbed discount
            $(div).find("input[type=checkbox].discount-airbed:checked").each(function (index, el) {
                fee = fee - 20;
                regoInfo.DiscountAirBed = 1;
            });


            //find the airport transfer
            $(div).find("input[type=checkbox].airport-transfer:checked").each(function (index, el) {
                fee = fee + 25;
                regoInfo.AirportTransfer = 1;
            });

        }

        if (fee < 0) { fee = 0 }


        //display the fee
        $(div).find(".line-total").each(function () {
            $(this).html("$ " + fee.toFixed(2))//.fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
                
        });



        regoInfo.Age = age;
        regoInfo.Name = $(div).find("input[type=text].name").val();
        regoInfo.Fee = fee;
        regoInfo.Relation = $(div).find("select.relation:first").val()

        return regoInfo


    }


    $.validator.addMethod("age", function (value, element) {
        //alert(value)
        if ($.trim(value) == "") {
            return true;
        }
        return !this.optional(element) && !this.optional($(element).parent().prev().children("input[type=text].name")[0]);
    }, "Name &amp; Age required");



    $(function () {

        attachEventsToMainContact();
        attachEventsToRegistrants();

        $("#rego-form").validate({
            debug: false,
            errorClass: "validate-error",
            focusInvalid: false,
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
                //updateRowFee(el)
                collectRegistrantInfo()
            })
        });
    }

    function attachEventsToMainContact() {
        //attach to main contact
        $(".form-horizontal input, .form-horizontal select").each(function (index, el) {
            $(el).bind("change", function () {
                //updateRowFee(el)
                collectRegistrantInfo()
            })
        });
   
    }


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
        utilsScript: "js/utils.js"
    });
