const openLogin = document.getElementById('openLogin');
const closeLogin = document.getElementById('closeLogin');
const loginModal = document.getElementById('loginModal');
const loginForm = document.getElementById('loginForm');

// abre el modal
openLogin.addEventListener('click', () => {
    loginModal.classList.add('active');
});

// cierra el modal
closeLogin.addEventListener('click', () => {
    loginModal.classList.remove('active');
});

// se cierra con el click
window.addEventListener('click', (e) => {
    if (e.target === loginModal) {
        loginModal.classList.remove('active');
    }
});

// log in
loginForm.addEventListener('submit', (e) => {

    e.preventDefault();

    const usuario = document.getElementById('usuario').value;
    const password = document.getElementById('password').value;

    // user dem
    if (usuario === 'admin' && password === '1234') {

        alert('Bienvenido Administrador');

        // red
        window.location.href = 'XLR8/inicio.html';

    } else {

        alert('Usuario o contraseña incorrectos');
    }
});
console.log("JS conectado");