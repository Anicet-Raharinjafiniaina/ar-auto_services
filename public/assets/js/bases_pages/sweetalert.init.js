document.addEventListener('DOMContentLoaded', function () {
    function safeAddEventListener(id, callback) {
        var el = document.getElementById(id);
        if (el) el.addEventListener("click", callback);
    }

    safeAddEventListener("sa-basic", function () {
        Swal.fire({
            title: "Any fool can use a computer",
            confirmButtonColor: "#1c84ee"
        });
    });

    safeAddEventListener("sa-title", function () {
        Swal.fire({
            title: "The Internet?",
            text: "That thing is still around?",
            icon: "question",
            confirmButtonColor: "#1c84ee"
        });
    });

    safeAddEventListener("sa-success", function () {
        Swal.fire({
            title: "Good job!",
            text: "You clicked the button!",
            icon: "success",
            showCancelButton: true,
            confirmButtonColor: "#1c84ee",
            cancelButtonColor: "#fd625e"
        });
    });

    safeAddEventListener("sa-warning", function () {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#1c84ee",
            cancelButtonColor: "#fd625e",
            confirmButtonText: "Yes, delete it!"
        }).then(function (e) {
            if (e.value) {
                Swal.fire("Deleted!", "Your file has been deleted.", "success");
            }
        });
    });

    safeAddEventListener("sa-params", function () {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            confirmButtonClass: "btn btn-success mt-2",
            cancelButtonClass: "btn btn-danger ms-2 mt-2",
            buttonsStyling: false
        }).then(function (e) {
            if (e.value) {
                Swal.fire({
                    title: "Deleted!",
                    text: "Your file has been deleted.",
                    icon: "success",
                    confirmButtonColor: "#1c84ee"
                });
            } else if (e.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({
                    title: "Cancelled",
                    text: "Your imaginary file is safe :)",
                    icon: "error",
                    confirmButtonColor: "#1c84ee"
                });
            }
        });
    });

    safeAddEventListener("sa-image", function () {
        Swal.fire({
            title: "Sweet!",
            text: "Modal with a custom image.",
            imageUrl: "assets/images/logo-sm.svg",
            imageHeight: 48,
            confirmButtonColor: "#1c84ee",
            animation: false
        });
    });

    safeAddEventListener("sa-close", function () {
        var timerInterval;
        Swal.fire({
            title: "Auto close alert!",
            html: "I will close in <b></b> seconds.",
            timer: 2000,
            timerProgressBar: true,
            didOpen: function () {
                Swal.showLoading();
                timerInterval = setInterval(function () {
                    var b = Swal.getHtmlContainer().querySelector("b");
                    if (b) {
                        b.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
                    }
                }, 100);
            },
            willClose: function () {
                clearInterval(timerInterval);
            }
        }).then(function (e) {
            if (e.dismiss === Swal.DismissReason.timer) {
                console.log("I was closed by the timer");
            }
        });
    });

    safeAddEventListener("custom-html-alert", function () {
        Swal.fire({
            title: "<i>HTML</i> <u>example</u>",
            icon: "info",
            html: 'You can use <b>bold text</b>, <a href="//Pichforest.in/">links</a> and other HTML tags',
            showCloseButton: true,
            showCancelButton: true,
            confirmButtonClass: "btn btn-success",
            cancelButtonClass: "btn btn-danger ms-1",
            confirmButtonColor: "#47bd9a",
            cancelButtonColor: "#fd625e",
            confirmButtonText: '<i class="fas fa-thumbs-up me-1"></i> Great!',
            cancelButtonText: '<i class="fas fa-thumbs-down"></i>'
        });
    });

    safeAddEventListener("sa-position", function () {
        Swal.fire({
            position: "top-end",
            icon: "success",
            title: "Your work has been saved",
            showConfirmButton: false,
            timer: 1500
        });
    });

    safeAddEventListener("custom-padding-width-alert", function () {
        Swal.fire({
            title: "Custom width, padding, background.",
            width: 600,
            padding: 100,
            confirmButtonColor: "#1c84ee",
            background: "#e0e1f3"
        });
    });

    safeAddEventListener("ajax-alert", function () {
        Swal.fire({
            title: "Submit email to run ajax request",
            input: "email",
            showCancelButton: true,
            confirmButtonText: "Submit",
            showLoaderOnConfirm: true,
            confirmButtonColor: "#1c84ee",
            cancelButtonColor: "#fd625e",
            preConfirm: function (email) {
                return new Promise(function (resolve, reject) {
                    setTimeout(function () {
                        if (email === "taken@example.com") {
                            reject("This email is already taken.");
                        } else {
                            resolve();
                        }
                    }, 2000);
                });
            },
            allowOutsideClick: false
        }).then(function (result) {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: "success",
                    title: "Ajax request finished!",
                    confirmButtonColor: "#1c84ee",
                    html: "Submitted email: " + result.value
                });
            }
        });
    });
});
