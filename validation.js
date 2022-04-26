
function validate() {
    // alert("validating");
    var email = document.getElementById("email").value;
    var pass = document.getElementById("password").value;

    if (email.length > 0 && pass.length > 5) {
        return true;
    }

    alert("Please enter a long enough email and password.")
    return false;
}



//ready everything up
$(document).ready(function() {
    // alert("monke");



// Password validate function has default length 5, but can
// be updated by parameter
function passwordValidate(len=5) {
    var pass = document.getElementById("password");
    var submit = document.getElementById("submit");
    var pwhelp = document.getElementById("pwhelp");
    var passval = pass.value;

    if (passval.length < len) {
        pass.classList.add("is-invalid");
        submit.disabled = true;
        pwhelp.textContent = "Your password must be "+len +" characters long.";
    } else {
        pass.classList.remove("is-invalid");
        submit.disabled = false;
        pwhelp.textContent = "";
    }
}

// Set the on blur event to call our passwordValidate handler
// document.getElementById("password").onblur  = passwordValidate;


//JQUERY 
$("#password").keyup(function(){
    passwordValidate();
});

});

