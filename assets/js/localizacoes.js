document.addEventListener("DOMContentLoaded", function() {
    
    let localizacoes = [
        { id: 1, servico: "Unidade de Cuidados Intensivos (UCI)", edificio: "Edifício Principal", piso: "Piso 3" },
        { id: 2, servico: "Urgência", edificio: "Edifício Sul", piso: "Piso 0" },
        { id: 3, servico: "Bloco Operatório", edificio: "Edifício Principal", piso: "Piso 2" },
        { id: 4, servico: "Imagiologia", edificio: "Edifício Norte", piso: "Piso -1" },
        { id: 5, servico: "Medicina Interna", edificio: "Edifício Principal", piso: "Piso 4" },
        { id: 6, servico: "Ortopedia", edificio: "Edifício Nascente", piso: "Piso 1" }
    ];

    const grid = document.getElementById("grid-localizacoes");
    const form = document.getElementById("form-localizacao");

    function renderizarLocalizacoes() {
        grid.innerHTML = "";
        
        localizacoes.forEach(loc => {
            const card = document.createElement("div");
            card.className = "col-md-6 col-lg-4";
            card.innerHTML = `
                <div class="card border-0 shadow-sm h-100 feature-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary me-3">
                                <i class="fas fa-map-marker-alt fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">${loc.servico}</h5>
                            </div>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted small fw-bold text-uppercase">Edifício:</span>
                            <span class="text-dark fw-medium ms-1">${loc.edificio}</span>
                        </div>
                        <div>
                            <span class="text-muted small fw-bold text-uppercase">Piso:</span>
                            <span class="text-dark fw-medium ms-1">${loc.piso}</span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top py-3 text-end">
                        <button class="btn btn-sm btn-outline-secondary fw-bold me-1"><i class="fas fa-edit"></i> Editar</button>
                        <button class="btn btn-sm btn-outline-danger fw-bold"><i class="fas fa-trash"></i> Apagar</button>
                    </div>
                </div>
            `;
            grid.appendChild(card);
        });
    }

    renderizarLocalizacoes();

    if (form) {
        form.addEventListener("submit", function(event) {
            event.preventDefault();
            
            const servico = document.getElementById("input-nome-loc").value;
            const edificio = document.getElementById("input-edificio").value;
            const piso = document.getElementById("input-piso").value;

            localizacoes.push({
                id: Date.now(),
                servico: servico,
                edificio: edificio,
                piso: piso
            });

            renderizarLocalizacoes();

            const modalElement = document.getElementById('modalNovaLocalizacao');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            modalInstance.hide();

            form.reset();
        });
    }
});