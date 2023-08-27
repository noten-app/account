const password_fields = $('input[type="password"]');
const password_requirements_lmnt = document.getElementById("password_requirements");

const password_requirement_same = document.getElementById("password_requirement-same");
const password_requirement_length = document.getElementById("password_requirement-length");
const password_requirement_uppercase = document.getElementById("password_requirement-uppercase");
const password_requirement_lowercase = document.getElementById("password_requirement-lowercase");
const password_requirement_number = document.getElementById("password_requirement-number");
const password_requirement_special = document.getElementById("password_requirement-special");

const login_button = document.getElementById("login_button");

// Password requirements show/hide

password_fields.on('focus', function () {
    password_requirements.show();
});

password_fields.on('blur', function () {
    password_requirements.hide();
});

let password_requirements = {
    show: () => {
        password_requirements_lmnt.style.height = '9em';
        password_requirements_lmnt.style.padding = '0.5rem';
        password_requirements_lmnt.style.marginBottom = '3rem';
    },
    hide: () => {
        password_requirements_lmnt.style.height = '0';
        password_requirements_lmnt.style.padding = '0';
        password_requirements_lmnt.style.marginBottom = '2rem';
    },
    status: (requirement, status) => {
        if (status) {
            requirement.style.color = '#0f0';
            requirement.innerHTML = '&#10004;';
        } else {
            requirement.style.color = '#f00';
            requirement.innerHTML = '&#10006;';
        }
        password_requirements.status_list[requirement.id.toString().replace('password_requirement-', '')] = status;
        if (Object.values(password_requirements.status_list).every((val) => val === true)) {
            login_button.disabled = false;
        } else login_button.disabled = true;
    },
    status_list: {
        same: false,
        length: false,
        uppercase: false,
        lowercase: false,
        number: false,
        special: false
    }
}

// Password requirements check

password_fields.on('keyup', () => {

    // Check password length | 8 < length < 72
    password_requirements.status(password_requirement_length, password_fields.val().length >= 8 && password_fields.val().length <= 72);

    // Check passwords same
    password_requirements.status(password_requirement_same, password_fields[0].value === password_fields[1].value);

    // Check password contains special characters
    password_requirements.status(password_requirement_special, /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password_fields.val()));

    // Check password contains uppercase
    password_requirements.status(password_requirement_uppercase, /[A-Z]/.test(password_fields.val()));

    // Check password contains lowercase
    password_requirements.status(password_requirement_lowercase, /[a-z]/.test(password_fields.val()));

    // Check password contains number
    password_requirements.status(password_requirement_number, /[0123456789]/.test(password_fields.val()));
});
