function login() {
    const formData = new FormData();
    //read the email and password input field values or use the default values
    const email = document.getElementById("email").value || "hello@ysi.com";
    const password = document.getElementById("password").value || "1234";
    //append the email, password and request_type values to the form data
    formData.append("email", email);
    formData.append("password", password);
    formData.append("request_type", "login");
    //make a request to login a user
    fetch("server.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            console.log("Success:", result);
        })
        .catch(error => {
            console.error("Error:", error);
        });
}

function register() {
    const formData = new FormData();
    //read the uploaded profile image field
    const image = document.querySelector('input[type="file"]');
    //read the email, username, password and confirm_password input field values or use the default values
    const email = document.getElementById("email").value || "hello@ysi.com";
    const username = document.getElementById("username").value || "ysi";
    const password = document.getElementById("password").value || "1234";
    const confirm_password =
        document.getElementById("confirm_password").value || "1234";
    //append the email, username, password, confirm_password, profile_image and request_type data to the form data
    formData.append("email", email);
    formData.append("username", username);
    formData.append("password", password);
    formData.append("confirm_password", confirm_password);
    formData.append("profile_image", image.files[0]);
    formData.append("request_type", "register");
    //make a request to create an account
    fetch("server.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            console.log("Success:", result);
        })
        .catch(error => {
            console.error("Error:", error);
        });
}

function retrieve_session() {
    const formData = new FormData();
    //append the request_type data to the form data
    formData.append("request_type", "retrieve_session");
    //make a request to fetch a logged in user's session data
    fetch("server.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            console.log("Success:", result);
        })
        .catch(error => {
            console.error("Error:", error);
        });
}

function logout() {
    const formData = new FormData();
    //append the request_type data to the form data
    formData.append("request_type", "logout");
    //make a request to destroy the session data and log out the user.
    fetch("server.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            console.log("Success:", result);
        })
        .catch(error => {
            console.error("Error:", error);
        });
}