function togglePassword(inputId, iconElement) {
    let input = document.getElementById(inputId);
    let icon = iconElement.querySelector("i");

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
    }
}

// Add event listeners to icons
document.getElementById("togglePassword").addEventListener("click", function () {
    togglePassword("password", this);
});

document.getElementById("toggleCPassword").addEventListener("click", function () {
    togglePassword("cpassword", this);
});
