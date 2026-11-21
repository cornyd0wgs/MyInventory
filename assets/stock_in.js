function initStockIn() {
    const wrapper = document.querySelector('[data-stock-in]');
    if (!wrapper) {
        return;
    }

    const collection = wrapper.querySelector('[data-stock-items]');
    const addButton = wrapper.querySelector('[data-add-stock-item]');
    const barcodeField = wrapper.querySelector('[data-stock-barcode]');
    const barcodeButton = wrapper.querySelector('[data-barcode-add]');
    const barcodeError = wrapper.querySelector('[data-barcode-error]');
    const lookupUrl = wrapper.dataset.lookupUrl;
    const prototype = collection ? (collection.dataset.prototype || '') : '';
    let index = Number.parseInt(collection ? (collection.dataset.index || '0') : '0', 10);

    function toCents(value) {
        const match = /^(\d+)(?:\.(\d{1,2}))?$/.exec(String(value || '').trim());
        if (!match) {
            return 0;
        }

        const pesos = Number.parseInt(match[1], 10) || 0;
        const centavos = Number.parseInt((match[2] || '').padEnd(2, '0'), 10) || 0;

        return pesos * 100 + centavos;
    }

    function formatCents(cents) {
        const pesos = Math.floor(cents / 100);
        const centavos = String(cents % 100).padStart(2, '0');

        return '₱' + pesos.toLocaleString('en-PH') + '.' + centavos;
    }

    function refreshRow(row) {
        const subtotal = row.querySelector('[data-subtotal]');
        if (!subtotal) {
            return;
        }

        const quantityField = row.querySelector('[data-quantity]');
        const costField = row.querySelector('[data-unit-cost]');
        const quantity = Number.parseInt(quantityField ? quantityField.value : '0', 10) || 0;
        const cents = toCents(costField ? costField.value : '0');

        subtotal.textContent = formatCents(quantity * cents);
    }

    function refreshAll() {
        if (!collection) {
            return;
        }

        collection.querySelectorAll('.stock-item-row').forEach(refreshRow);
    }

    function addRow(values = {}) {
        if (!collection || !prototype) {
            return null;
        }

        const holder = document.createElement('div');
        holder.innerHTML = prototype.replace(/__name__/g, String(index++));

        const row = holder.firstElementChild;
        if (!row) {
            return null;
        }

        if (values.productId !== undefined) {
            const select = row.querySelector('[data-product-select]');
            if (select) {
                select.value = String(values.productId);
            }
        }

        if (values.quantity !== undefined) {
            const quantity = row.querySelector('[data-quantity]');
            if (quantity) {
                quantity.value = String(values.quantity);
            }
        }

        if (values.unitCost !== undefined) {
            const cost = row.querySelector('[data-unit-cost]');
            if (cost) {
                cost.value = String(values.unitCost);
            }
        }

        collection.appendChild(row);
        refreshRow(row);

        return row;
    }

    function showError(message) {
        if (!barcodeError) {
            return;
        }

        barcodeError.textContent = message;
        barcodeError.classList.remove('d-none');
    }

    function hideError() {
        if (!barcodeError) {
            return;
        }

        barcodeError.textContent = '';
        barcodeError.classList.add('d-none');
    }

    async function scanBarcode() {
        if (!barcodeField || !lookupUrl) {
            return;
        }

        const barcode = barcodeField.value.trim();
        if (!barcode) {
            showError('Type or scan a barcode first.');
            barcodeField.focus();

            return;
        }

        hideError();
        if (barcodeButton) {
            barcodeButton.setAttribute('disabled', 'disabled');
        }

        try {
            const response = await fetch(lookupUrl + '?barcode=' + encodeURIComponent(barcode), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                showError(data.error || 'No active product found for that barcode.');

                return;
            }

            addRow({ productId: data.id, quantity: 1, unitCost: data.unitCost });
            barcodeField.value = '';
            barcodeField.focus();
        } catch (error) {
            showError('Barcode lookup failed. Use the product dropdown instead.');
        } finally {
            if (barcodeButton) {
                barcodeButton.removeAttribute('disabled');
            }
        }
    }

    if (collection) {
        collection.addEventListener('input', (event) => {
            const row = event.target.closest('.stock-item-row');
            if (row) {
                refreshRow(row);
            }
        });

        collection.addEventListener('click', (event) => {
            const button = event.target.closest('[data-remove-stock-item]');
            if (!button) {
                return;
            }

            event.preventDefault();
            const row = button.closest('.stock-item-row');
            if (row) {
                row.remove();
                refreshAll();
            }
        });
    }

    if (addButton) {
        addButton.addEventListener('click', (event) => {
            event.preventDefault();
            addRow({ quantity: 1 });
        });
    }

    if (barcodeField) {
        barcodeField.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                scanBarcode();
            }
        });
        barcodeField.focus();
    }

    if (barcodeButton) {
        barcodeButton.addEventListener('click', (event) => {
            event.preventDefault();
            scanBarcode();
        });
    }

    refreshAll();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initStockIn);
} else {
    initStockIn();
}
