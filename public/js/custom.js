// Allow digits only

(function($) {
    $.fn.inputFilterDigits = function(callback, errMsg) {
        return this.on("input keydown keyup mousedown mouseup select contextmenu drop focusout", function(e) {
            if (callback(this.value)) {
                // Accepted value
                if (["keydown","mousedown","focusout"].indexOf(e.type) >= 0){
                    $(this).removeClass("is-invalid");
                    this.setCustomValidity("");
                    $('.digits-input').html("");
                }
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                // Rejected value - restore the previous one
                $(this).addClass("is-invalid");
                this.setCustomValidity(errMsg);
                $('.digits-input').html(errMsg);
                // this.reportValidity();
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            } else {
                // Rejected value - nothing to restore
                this.value = "";
            }
        });
    };
}(jQuery));

// Allow non-repeating digits only
(function($) {
    $.fn.inputFilterRepeatingDigits = function(callback, errMsg) {
        return this.on("input keydown keyup mousedown mouseup select contextmenu drop focusout", function(e) {
            if (callback(this.value)) {
                this.value = this.value.slice(0, -1);
                $(this).addClass("is-invalid");
                this.setCustomValidity(errMsg);
                $('.digits-input').html(errMsg);
            }
        });
    };
}(jQuery));

// if in use, digits 1 and 8 should be right next to each other
(function($) {
    $.fn.inputFilterEachOtherDigits = function(callback, errMsg) {
        return this.on("input keydown keyup mousedown mouseup select contextmenu drop focusout", function(e) {
            if (callback(this.value)) {
                if (this.value.match(18) || this.value.match(81)) {
                    // move on
                } else if (this.value.match(1) && (this.value.match(8))) {
                    $(this).addClass("is-invalid");
                    this.setCustomValidity(errMsg);
                    $('.digits-input').html(errMsg);
                }
            }
        });
    };
}(jQuery));

// if in use, digits 4 and 5 shouldn't be on even index / position
(function($) {
    $.fn.inputFilterEvenIndex = function(callback, errMsg) {
        return this.on("input keydown keyup mousedown mouseup select contextmenu drop focusout", function(e) {
            if (callback(this.value)) {
                if (this.value.match(4) && this.value.match(5)) {
                    var digits = this.value.split('');
                    for (i = 0; i <= digits.length; i++) {
                        if ((digits[i] == 4 || digits[i] == 5) && digits.indexOf(digits[i]) % 2 == 1) {
                            $(this).addClass("is-invalid");
                            this.setCustomValidity(errMsg);
                            $('.digits-input').html(errMsg);
                        }
                    }
                } 
            }
        });
    };
}(jQuery));


$(document).ready(function() {
    $("#inputLarge").inputFilterDigits(function(value) {
        return /^\d*$/.test(value); // Allow only digits, using a RegExp
    },"Only digits allowed");

    $("#inputLarge").inputFilterRepeatingDigits(function(value) {
        return /([0-9]).*?\1/.test(value); // Allow only non-repeating digits, using a RegExp
    },"Only non-repeating digits allowed");

    $("#inputLarge").inputFilterEachOtherDigits(function(value) {
        return value; // if in use, digits 1 and 8 should be right next to each other
    },"If in use, digits 1 and 8 should be right next to each other");

    $("#inputLarge").inputFilterEvenIndex(function(value) {
        return value; // if in use, digits 4 and 5 shouldn't be on even index / position
    },"If in use, digits 4 and 5 shouldn't be on even index / position");


    // generated number
    var winNum;

    // Play button actions
    $(".play-btn").click(function() {

        // the game begins
        $.ajax({
            type : 'get',
            url : 'http://localhost/start',
            data:{'start':'yes'},
            success:function(data){
                console.log(data);
                winNum = data;
            }
        });

        $("#inputLarge").removeAttr('readonly');
        $(".play-btn").hide();
        $(".stop-btn").show();

        // game time begins
        $("#timing").show();
        $('#clock').stopwatch().stopwatch('start');  

    });

    // Send input number
    $("#inputLarge").keyup(function() {
        if (this.value.length == 4) {
            if ($("#inputLarge").hasClass("is-invalid")) {
                
            } else {

                // the row count starts from 1
                var rowNumber = $("#rounds tr").length + 1;

                // ready to submit the number
                $.ajax({
                    type : 'get',
                    url : 'http://localhost/match',
                    data:{'enteredNumber':this.value, 'rowNumber':rowNumber},
                    success:function(data){
                        $('#rounds').append(data);
                        
                        if ($(".table-success")[0]){
                            // Do something if class exists
                            var winMsgModal = new bootstrap.Modal(document.getElementById("winMsg"), {});
                            winMsgModal.show();

                            $('.modal-body').css('font-size', '18px').html("Exactly right! The generated number is <span style='color: lime'>"+winNum+"</span>. Your score is " + "<span style='color: lime'>" + (1000 - rowNumber) + "</span> points");

                            // Reset game
                            $(".stop-btn").click();
                        }
                    }
                });

                this.value = "";
            }
        }
    });


    // Stop button actions
    $(".stop-btn").click(function() {
        $("#inputLarge").attr('readonly', 'readonly');
        $("#inputLarge").val("");
        $(".play-btn").show();
        $(".stop-btn").hide();

        // game time stop
        $("#timing").hide();
        $('#clock').stopwatch('stop');
        $('#clock').stopwatch('reset');

        $('#rounds').html("");
    });
    
});

