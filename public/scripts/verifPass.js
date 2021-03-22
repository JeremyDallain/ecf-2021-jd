const passInput = document.querySelector("#registration_form_plainPassword");

let verifNbDeCaractere = (length) => {
    if (passInput.value.length >= length) {
        document.querySelector("#pass_length_valid").classList.remove('d-none');
        return true;
    } else {
        document.querySelector("#pass_length_valid").classList.add('d-none');
        return false;
    }
}

let verifMajuscule = () => {
    let regexMaj = /[A-Z]/;
    if (regexMaj.test(passInput.value)) {
        document.querySelector("#pass_maj_valid").classList.remove('d-none');
        return true;
    } else {
        document.querySelector("#pass_maj_valid").classList.add('d-none');
        return false;
    }
}

let verifMinuscule = () => {
    let regexMin = /[a-z]/;
    if (regexMin.test(passInput.value)) {
        document.querySelector("#pass_min_valid").classList.remove('d-none');
        return true;
    } else {
        document.querySelector("#pass_min_valid").classList.add('d-none');
        return false;
    }
}

let verifChiffre = () => {
    let regexNumber = /[0-9]/;
    if (regexNumber.test(passInput.value)) {
        document.querySelector("#pass_number_valid").classList.remove('d-none');
        return true;
    } else {
        document.querySelector("#pass_number_valid").classList.add('d-none');
        return false;
    }
}

let verifAllContraintes = () => {
    if (verifNbDeCaractere(8) && verifMajuscule() && verifMinuscule() && verifChiffre()) {
        passInput.classList.add('bg-success');
        passInput.classList.remove('bg-light');
        passInput.classList.remove('bg-danger');
        return true;
    } else {
        passInput.classList.add('bg-light');
        passInput.classList.remove('bg-success');
        passInput.classList.remove('bg-danger');
        return false;
    }
}

passInput.addEventListener('input', () => {
    verifNbDeCaractere(8);
    verifMajuscule();
    verifMinuscule();
    verifChiffre();
    verifAllContraintes();
})


const formRegister = document.querySelector("#form_register");
formRegister.addEventListener('submit', (e) => {
    if (!verifAllContraintes()) {
        e.preventDefault();
        passInput.classList.remove('bg-light');
        passInput.classList.remove('bg-success');
        passInput.classList.add('bg-danger');
    }
})