// ============================================================
// Pagination
// ============================================================

function previousPage(event) {

    event.preventDefault();

    if (paginationState.currentPage <= 1) {
        return;
    }

    paginationState.currentPage--;

    fetchEmployees();
}


function nextPage(event) {

    event.preventDefault();

    if (
        paginationState.currentPage >=
        paginationState.totalPages
    ) {
        return;
    }

    paginationState.currentPage++;

    fetchEmployees();
}


function updatePaginationControls() {

    const container = document.getElementById(
        'paginationContainer'
    );

    const pageIndicator = document.getElementById(
        'pageIndicator'
    );

    const prevBtn = document.getElementById(
        'prevBtn'
    );

    const nextBtn = document.getElementById(
        'nextBtn'
    );

    if (paginationState.totalPages <= 1) {

        container.style.display = 'none';

        return;
    }

    container.style.display = 'block';

    pageIndicator.textContent =
        `Page ${paginationState.currentPage} of ${paginationState.totalPages}`;

    prevBtn.classList.toggle(
        'disabled',
        paginationState.currentPage === 1
    );

    nextBtn.classList.toggle(
        'disabled',
        paginationState.currentPage ===
        paginationState.totalPages
    );
}
