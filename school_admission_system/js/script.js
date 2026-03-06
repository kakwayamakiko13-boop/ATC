function validateRegister(){

    let password = document.getElementById("password").value;
    let confirm = document.getElementById("confirm").value;

    if(password.length < 6){
        alert("Password must be at least 6 characters");
        return false;
    }

    if(password !== confirm){
        alert("Passwords do not match");
        return false;
    }

    return true;
}