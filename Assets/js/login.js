const formLogin = document.getElementById('form-login');
const msgErro = document.getElementById('mensagem-erro');

formLogin.addEventListener('submit', function(evento) {
    // Impede a página de recarregar quando clicamos em "Entrar"
    evento.preventDefault();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    // Credenciais de simulação para o professor testar
    const emailCorreto = "admin@biotrack.com";
    const passwordCorreta = "sofia4";

    if (email === emailCorreto && password === passwordCorreta) {
        // Se acertou, regista a sessão no browser e entra
        localStorage.setItem('biotrack_sessao', 'ativa');
        window.location.href = '../private/dashboard.html';
    } else {
        // Se errou, mostra a caixa vermelha de erro
        msgErro.textContent = "Acesso Negado: Email ou palavra-passe incorretos.";
        msgErro.classList.remove('d-none');
        
        // Esconde o erro automaticamente após 3 segundos
        setTimeout(() => {
            msgErro.classList.add('d-none');
        }, 3000);
    }
});