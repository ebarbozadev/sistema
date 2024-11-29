<div class="form-group">
    <div class="form-group" style="position: relative;">
        <label for="client-search">Pesquisar Cliente</label>
        <input
            type="text"
            id="client-search"
            name="client_search"
            class="form-control"
            placeholder="Digite o nome ou ID do cliente"
            autocomplete="off" />
        <div id="client-suggestions"></div>
    </div>

    <div class="form-group">
        <label for="selected-client">Cliente Selecionado</label>
        <input
            type="text"
            id="selected-client"
            name="selected_client"
            class="form-control"
            readonly />
    </div>

    <div class="form-group">
        <label for="vendedor">Vendedor</label>
        <input
            type="text"
            id="vendedor"
            name="vendedor"
            class="form-control"
            value="{{ Auth::user()->name }}"
            readonly />
    </div>
</div>

<style>
    #client-suggestions {
        position: absolute;
        z-index: 1000;
        width: 100%;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 4px;
        max-height: 200px;
        overflow-y: auto;
        display: none;
    }

    #client-suggestions a {
        display: block;
        padding: 8px;
        text-decoration: none;
        color: #333;
        cursor: pointer;
    }

    #client-suggestions a:hover {
        background-color: #f8f9fa;
    }
</style>

<script>
    (() => {
        const clientSearchInput = document.getElementById("client-search");
        const selectedClientInput = document.getElementById("selected-client");
        const suggestionsBox = document.getElementById("client-suggestions");

        // Atualiza o campo "Cliente Selecionado"
        function selectClient(client) {
            selectedClientInput.value = `${client.name} (ID: ${client.id})`;
            clientSearchInput.value = "";
            suggestionsBox.style.display = "none";
        }

        // Pesquisa de clientes em tempo real
        clientSearchInput.addEventListener("input", function() {
            const query = this.value;

            if (query.length > 0) { // Permite buscar mesmo com 1 caractere para IDs
                fetch(`/admin/search-clients?query=${query}`)
                    .then((response) => response.json())
                    .then((clients) => {
                        suggestionsBox.innerHTML = "";

                        if (clients.length > 0) {
                            suggestionsBox.style.display = "block";

                            clients.forEach((client) => {
                                const suggestionItem = document.createElement("a");
                                suggestionItem.textContent = `${client.name} (ID: ${client.id})`;
                                suggestionItem.dataset.id = client.id;
                                suggestionItem.classList.add("dropdown-item");

                                suggestionItem.addEventListener("click", function(e) {
                                    e.preventDefault();
                                    selectClient(client);
                                });

                                suggestionsBox.appendChild(suggestionItem);
                            });
                        } else {
                            suggestionsBox.style.display = "none";
                        }
                    })
                    .catch((error) => console.error("Erro ao buscar clientes:", error));
            } else {
                suggestionsBox.style.display = "none";
            }
        });

        // Esconde as sugestões se clicar fora do campo
        document.addEventListener("click", function(e) {
            if (!clientSearchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                suggestionsBox.style.display = "none";
            }
        });
    })();
</script>