document.addEventListener("DOMContentLoaded", () => {
    const elements = {
        loginForm: document.getElementById("login-form"),
        emailInput: document.getElementById("email"),
        senhaInput: document.getElementById("senha"),
        responseMessage: document.getElementById("response-message"),
        togglePassword: document.getElementById("toggle-password"),
        eyeIcon: document.getElementById("eye-icon")
    };

    // INICIALIZA EVENTOS AO CARREGAR A PÁGINA
    init();

    // CONFIG DOS EVENTOS
    function init() {
        // FORMULARIO LOGIN
        elements.loginForm.addEventListener("submit", enviarLogin);

        // MOSTRAR/OCULTAR SENHA
        elements.togglePassword.addEventListener("click", togglePasswordVisibility);
    }

    // FORMULARIO LOGIN
    async function enviarLogin(event) {
        event.preventDefault();

        const email = elements.emailInput.value;
        const senha = elements.senhaInput.value;

        try {
            const response = await fetch("./api/login.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ email, senha }),
            });

            const data = await response.json();

            if (data.status === "success" && data.user) {
                redirectDashboard(data.user.tipo);
            } else {
                elements.responseMessage.innerHTML = `<p class="text-danger">${data.message || "Erro no login"}</p>`;
            }
        } catch (error) {
            console.error("Erro:", error);
        }
    }

    // REDIRECIONAR USUARIO DE ACORDO COM O TIPO
    function redirectDashboard(userType) {
        if (userType === "aluno") {
            window.location.href = "/aluno";
        } else if (userType === "professor") {
            window.location.href = "/professor";
        } else if (userType === "coordenador") {
            window.location.href = "/coordenador";
        }
    }

    // MOSTRAR/OCULTAR SENHA
    function togglePasswordVisibility() {
        const type = elements.senhaInput.getAttribute('type') === 'password' ? 'text' : 'password';
        elements.senhaInput.setAttribute('type', type);

        if (type === 'text') {
            elements.eyeIcon.classList.remove('fa-eye');
            elements.eyeIcon.classList.add('fa-eye-slash');
        } else {
            elements.eyeIcon.classList.remove('fa-eye-slash');
            elements.eyeIcon.classList.add('fa-eye');
        }
    }
});