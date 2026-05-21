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
    
    equipamentos.forEach(equip => {
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
                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `;
        tabelaBody.innerHTML += linha;
    });
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

    equipamentos.push(novoEquipamento);
    localStorage.setItem('bd_equipamentos', JSON.stringify(equipamentos));
    
    renderizarTabela();
    formulario.reset();
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalNovoEquipamento'));
    modal.hide();
});

renderizarTabela();