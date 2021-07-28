document.addEventListener('DOMContentLoaded', function () {
    if (typeof document.forms[0] !== 'undefined') {
        let form = document.querySelectorAll("form");
        let timeoutFunc2 = false;

        if (form) {
            let input = document.querySelector("form").querySelectorAll("input");
            let submitButton = form[0].querySelector("button[type='submit']");
            let counter = 0;

            input.forEach(function (element) {
                counter++;
                let timeoutFunc = "timeout" + counter;

                if (element.hasAttribute("required") && (element.type !== "email" && element.type !== "password")) {

                    element.addEventListener("keydown", function () {

                        clearTimeout(timeoutFunc);

                        timeoutFunc = setTimeout(function() {
                            if (element.value === "") {
                                element.parentElement.querySelector("[data-info]").classList.add("false");
                                element.parentElement.querySelector("[data-info]").classList.remove("true");
                            } else {
                                element.parentElement.querySelector("[data-info]").classList.add("true");
                                element.parentElement.querySelector("[data-info]").classList.remove("false");
                            }
                        }, 500);

                    });
                }

                if (element.name === "username") {
                    element.addEventListener("keydown", function() {
                        clearTimeout(timeoutFunc2);
                        timeoutFunc2 = setTimeout(function() {
                            if (element.value !== "") {
                                console.log("todo: check if username exist");
                            }
                        }, 1000);
                    });
                }

                if (element.name === "email") {

                    element.addEventListener("focusout", function() {
                        let email_confirm = document.querySelector("form input[name='email_confirm']");


                        if (checkEmail(element.value)) {

                            element.parentElement.querySelector("[data-box='error']").classList.remove("active");
                            element.parentElement.querySelector("[data-info]").classList.add("true");
                            element.parentElement.querySelector("[data-info]").classList.remove("false");

                            if (element.value === email_confirm.value) {

                                if (email_confirm.parentElement.querySelector("[data-info]").classList.contains("false")) {
                                    email_confirm.parentElement.querySelector("[data-box='error']").classList.remove("active");
                                    email_confirm.parentElement.querySelector("[data-info]").classList.add("true");
                                    email_confirm.parentElement.querySelector("[data-info]").classList.remove("false");
                                }

                            } else {
                                email_confirm.parentElement.querySelector("[data-box='error']").classList.add("active");
                                email_confirm.parentElement.querySelector("[data-info]").classList.add("false");
                                email_confirm.parentElement.querySelector("[data-info]").classList.remove("true");
                            }
                        } else {

                            element.parentElement.querySelector("[data-box='error']").classList.add("active");
                            element.parentElement.querySelector("[data-info]").classList.add("false");
                            element.parentElement.querySelector("[data-info]").classList.remove("true");
                        }
                    });
                }

                if (element.name === "email_confirm") {
                    element.addEventListener("focusout", function() {
                        let email = document.querySelector("form input[name='email']");
                        if (email.value === element.value) {
                            element.parentElement.querySelector("[data-box='error']").classList.remove("active");
                            element.parentElement.querySelector("[data-info]").classList.add("true");
                            element.parentElement.querySelector("[data-info]").classList.remove("false");
                        } else {
                            element.parentElement.querySelector("[data-box='error']").classList.add("active");
                            element.parentElement.querySelector("[data-info]").classList.add("false");
                            element.parentElement.querySelector("[data-info]").classList.remove("true");
                        }
                    });
                }

                if (element.name === "password") {
                    element.addEventListener("keydown", function() {

                        clearTimeout(timeoutFunc);

                        timeoutFunc = setTimeout(function() {
                            let mode = checkPassword(element.value);

                            if (mode === 0) {
                                element.parentElement.querySelector("[data-info]").classList.add("false");
                                element.parentElement.querySelector("[data-info]").classList.remove("true");
                            } else {
                                element.parentElement.querySelector("[data-info]").classList.remove("false")
                                element.parentElement.querySelector("[data-info]").classList.add("true");
                            }
                            element.parentElement.querySelector("[data-info]").dataset.mode = checkPassword(element.value);
                        }, 100);

                    });
                }

            });

            form[0].addEventListener("submit", event => {
                event.preventDefault();
                let q = true;
                let submitNote = document.querySelector("[data-role='note']");
                let status = document.querySelectorAll("[data-info]");
                let redirect = this.querySelector("[data-redirect]").dataset.redirect;

                status.forEach(function(element) {
                    if (q === true) {
                        if (element.classList.contains("false")) {
                            submitNote.classList.add("active");
                            q = false;
                            return false;
                        } else {
                            q = true;
                            submitNote.classList.remove("active");
                        }
                    }
                });

                if (q === true) {
                    let note = document.querySelector("[data-role='note']");
                    ajaxPost(note, input, redirect);
                }
            });
        }

    }
});

/**
 * FUNCTIONS
 * */

const current_url = window.location.pathname;

function checkPassword(pw) {

    return /.{8,}/.test(pw) * (  /* at least 8 characters */
        /.{12,}/.test(pw)          /* bonus if longer */
        + /[a-z]/.test(pw)         /* a lower letter */
        + /[A-Z]/.test(pw)         /* a upper letter */
        + /\d/.test(pw)            /* a digit */
        //+ /[^A-Za-z0-9]/.test(pw)  /* a special character */
    )
}

function checkEmail (email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function ajaxPost(output, form, redirect = "false") {

    const data = new FormData();

    form.forEach(element => {
        data.append(element.name, element.value);
    });

    fetch(current_url, {
        method : "POST",
        redirect: "follow",
        body: data
    }).then(
        function(response) {
            response.text().then(function(data) {
                console.log(data);
                if (data !== '' && redirect === "true") {
                    window.location.href = data;
                } else {
                    console.log(data);
                }
            });

        /*
        response => {
            console.log(response.json());
            console.log(response);
            if (redirect !== "false") {
                window.location.href = redirect;
            }
        } */
    }).catch(function (error) {
        console.error(error);
    });

}

function checkLogin() {

    console.log("geht");

    fetch('https://my.bobring.dev/auth', {
        credentials: "include"
    })
        .then(res => {
            console.log(res);
            return res.text();
        })
        .then(data => console.log(data))
}
