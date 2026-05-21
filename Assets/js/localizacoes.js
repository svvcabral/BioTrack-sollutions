let localizacoes = JSON.parse(localStorage.getItem('bd_localizacoes')) || [
    { nome: "Cuidados Intensivos", edificio: "Edifício Principal", piso: "Piso 2", icone: "fa-procedures", equipamentos: 45 },
    { nome: "Urgência", edificio: "Edifício Principal", piso: "Piso 0", icone: "fa-ambulance", equipamentos: 112 },
    { nome: "Laboratório Central", edificio: "Edifício B", piso: "Piso -1", icone: "fa-microscope", equipamentos: 87 }
];

const gridLocalizacoes = document.getElementById('grid-localizacoes');
const formLocalizacao = document.getElementById('form-localizacao');

function renderizarGrid() {
    gridLocalizacoes.innerHTML = '';
    
    localizacoes.forEach((loc, index) => {
        const cartao = `
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-2 position-relative">
                    <button class="btn btn-sm text-danger position-absolute top-0 end-0 m-2 border-0 bg-transparent" onclick="apagarLocalizacao(${index})" title="Remover Localização">
                        <i class="fas fa-times fs-5"></i>
                    </button>
                    <div class="card-body text-center p-4">
                        <div class="text-primary mb-3">
                            <i class="fas ${loc.icone} fa-3x"></i>
                        </div>
                        <h4 class="fw-bold">${loc.nome}</h4>
                        <p class="text-muted small mb-4">${loc.edificio} • ${loc.piso}</p>
                        <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded">
                            <span class="small fw-bold text-secondary">Equipamentos Alocados:</span>
                            <span class="badge bg-primary fs-6">${loc.equipamentos}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        gridLocalizacoes.innerHTML += cartao;
    });
}

function apagarLocalizacao(index) {
    if (confirm("Tem a certeza que deseja remover esta localização do mapa do hospital?")) {
        localizacoes.splice(index, 1);
        localStorage.setItem('bd_localizacoes', JSON.stringify(localizacoes));
        renderizarGrid();
    }
}

formLocalizacao.addEventListener('submit', function(evento) {
    evento.preventDefault();

    const novaLocalizacao = {
        nome: document.getElementById('input-nome-loc').value,
        edificio: document.getElementById('input-edificio').value,
        piso: document.getElementById('input-piso').value,
        icone: "fa-door-open", // Ícone padrão para novas localizações
        equipamentos: 0 // Uma localização nova começa sem equipamentos
    };

    localizacoes.push(novaLocalizacao);
    localStorage.setItem('bd_localizacoes', JSON.stringify(localizacoes));
    
    renderizarGrid();
    formLocalizacao.reset();
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalNovaLocalizacao'));
    modal.hide();
});

renderizarGrid();