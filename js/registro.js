// Confirmação de senha
document.getElementById("form_registro").addEventListener("submit", function(event) {
        event.preventDefault();

        const senha1Input = document.getElementById("senha");
        const senha2Input = document.getElementById("senha2");
        const confirmarSenhaError = document.getElementById("SenhaErro");

        if (senha1Input.value !== senha2Input.value) {
            confirmarSenhaError.textContent = "As senhas não coincidem.";
            senha1Input.style.borderColor = "#ff0202ff";
            senha2Input.style.borderColor = "#ff0202ff";
        } else {
            confirmarSenhaError.textContent = "";
            senha1Input.style.borderColor = "";
            senha2Input.style.borderColor = "";
            alert("Senhas coincidem! Formulário enviado (apenas para demonstração).");
            // this.submit();
        }
    });

    document.getElementById("senha").addEventListener("input", function() {
        document.getElementById("SenhaErro").textContent = "";
        this.style.borderColor = "";
        document.getElementById("senha2").style.borderColor = "";
    });

    document.getElementById("senha2").addEventListener("input", function() {
        document.getElementById("SenhaErro").textContent = "";
        this.style.borderColor = "";
        document.getElementById("senha").style.borderColor = "";
    });