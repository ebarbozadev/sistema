<div class="form-group">
    <div class="form-group" style="position: relative;">
        <label for="supplier-search">Pesquisar Fornecedor</label>
        <input
            type="text"
            id="supplier-search"
            name="supplier_search"
            class="form-control"
            placeholder="Digite o nome ou ID do fornecedor"
            autocomplete="off" />
        <div id="supplier-suggestions"></div>
    </div>

    <div class="form-group">
        <label for="selected-supplier">Fornecedor Selecionado</label>
        <input
            type="text"
            id="selected-supplier"
            name="selected_supplier"
            class="form-control"
            readonly
            data-supplier-id="" />
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
        const supplierSearchInput = document.getElementById("supplier-search");
        const selectedSupplierInput = document.getElementById("selected-supplier");
        const suggestionsBox = document.getElementById("supplier-suggestions");

        function selectSupplier(supplier) {
            selectedSupplierInput.value = `${supplier.nome} (ID: ${supplier.id})`;
            selectedSupplierInput.dataset.supplierId = supplier.id;
            supplierSearchInput.value = "";
            suggestionsBox.style.display = "none";
        }

        supplierSearchInput.addEventListener("input", function() {
            const query = this.value;

            if (query.length > 0) {
                fetch(`/admin/c/search-suppliers?query=${query}`)
                    .then((response) => response.json())
                    .then((suppliers) => {
                        suggestionsBox.innerHTML = "";

                        if (suppliers.length > 0) {
                            suggestionsBox.style.display = "block";

                            suppliers.forEach((supplier) => {
                                const suggestionItem = document.createElement("a");
                                suggestionItem.textContent = `${supplier.nome} (ID: ${supplier.id})`;
                                suggestionItem.dataset.id = supplier.id;
                                suggestionItem.classList.add("dropdown-item");

                                suggestionItem.addEventListener("click", function(e) {
                                    e.preventDefault();
                                    selectSupplier(supplier);
                                });

                                suggestionsBox.appendChild(suggestionItem);
                            });
                        } else {
                            suggestionsBox.style.display = "none";
                        }
                    })
                    .catch((error) => console.error("Erro ao buscar fornecedores:", error));
            } else {
                suggestionsBox.style.display = "none";
            }
        });

        // Esconde as sugestões se clicar fora do campo
        document.addEventListener("click", function(e) {
            if (!supplierSearchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                suggestionsBox.style.display = "none";
            }
        });
    })();
</script>