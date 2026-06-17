let equipamentos = JSON.parse(localStorage.getItem('bd_equipamentos')) || [
    {
        codigo: "04.002",
        designacao: "Monitor Multiparamétrico",
        marca: "Philips IntelliVue MP5",
        localizacao: "Cuidados Intensivos (UCI)",
        criticidade: "Suporte de Vida"
    }
];

const tabelaBody = document.getElementById('tabela-equipamentos');
const formulario = document.getElementById('form-equipamento');

function renderizarTabela() {
    tabelaBody.innerHTML = '';
    
    equipamentos.forEach((equip, index) => {
        let badgeClass = "bg-primary";
        if (equip.criticidade === "Suporte de Vida") badgeClass = "bg-danger";
        if (equip.criticidade === "Alta") badgeClass = "bg-warning text-dark";

        const linha = `
            <tr>
                <td class="px-4 fw-bold text-secondary">${equip.codigo}</td>
                <td>${equip.designacao}</td>
                <td>${equip.marca}</td>
                <td>${equip.localizacao}</td>
                <td><span class="badge ${badgeClass}">${equip.criticidade}</span></td>
                <td class="px-4 text-end">
                    <button class="btn btn-sm btn-outline-danger" onclick="apagarEquipamento(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tabelaBody.innerHTML += linha;
    });
}

function apagarEquipamento(index) {
    if (confirm("Tem a certeza que deseja remover este equipamento do inventário?")) {
        equipamentos.splice(index, 1);
        localStorage.setItem('bd_equipamentos', JSON.stringify(equipamentos));
        renderizarTabela();
    }
}

formulario.addEventListener('submit', function(evento) {
    evento.preventDefault();

    const novoEquipamento = {
        codigo: document.getElementById('input-codigo').value,
        designacao: document.getElementById('input-designacao').value,
        marca: document.getElementById('input-marca').value,
        criticidade: document.getElementById('input-criticidade').value,
        localizacao: document.getElementById('input-localizacao').value
    };

    // Verificação simples para não duplicar códigos
    const codigoExiste = equipamentos.find(eq => eq.codigo === novoEquipamento.codigo);
    if (codigoExiste) {
        alert("Erro: Já existe um equipamento com este Código/Número de Série.");
        return;
    }

    equipamentos.push(novoEquipamento);
    localStorage.setItem('bd_equipamentos', JSON.stringify(equipamentos));
    
    renderizarTabela();
    formulario.reset();
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalNovoEquipamento'));
    modal.hide();
});

renderizarTabela();