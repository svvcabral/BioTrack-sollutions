let fornecedores = JSON.parse(localStorage.getItem('bd_fornecedores')) || [
    {
        empresa: "Philips Medical Systems",
        nif: "501234567",
        categoria: "Fabricante",
        email: "suporte@philips.pt",
        estado: "Contrato Ativo"
    }
];

const tabelaBody = document.getElementById('tabela-fornecedores');
const formulario = document.getElementById('form-fornecedor');

function renderizarTabela() {
    tabelaBody.innerHTML = '';
    
    fornecedores.forEach((forn, index) => {
        const linha = `
            <tr>
                <td class="px-4 fw-bold text-secondary">${forn.empresa}</td>
                <td>${forn.nif}</td>
                <td>${forn.categoria}</td>
                <td>${forn.email}</td>
                <td><span class="badge bg-success bg-opacity-10 text-success border border-success">${forn.estado}</span></td>
                <td class="px-4 text-end">
                    <button class="btn btn-sm btn-outline-danger" onclick="apagarFornecedor(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tabelaBody.innerHTML += linha;
    });
}

function apagarFornecedor(index) {
    if (confirm("Tem a certeza que deseja remover este fornecedor do sistema?")) {
        fornecedores.splice(index, 1);
        localStorage.setItem('bd_fornecedores', JSON.stringify(fornecedores));
        renderizarTabela();
    }
}

formulario.addEventListener('submit', function(evento) {
    evento.preventDefault();

    const novoFornecedor = {
        empresa: document.getElementById('input-empresa').value,
        nif: document.getElementById('input-nif').value,
        categoria: document.getElementById('input-categoria').value,
        email: document.getElementById('input-email').value,
        estado: "Contrato Ativo"
    };

    const nifExiste = fornecedores.find(f => f.nif === novoFornecedor.nif);
    if (nifExiste) {
        alert("Erro: Já existe um fornecedor com este NIF.");
        return;
    }

    fornecedores.push(novoFornecedor);
    localStorage.setItem('bd_fornecedores', JSON.stringify(fornecedores));
    
    renderizarTabela();
    formulario.reset();
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalNovoFornecedor'));
    modal.hide();
});

renderizarTabela();