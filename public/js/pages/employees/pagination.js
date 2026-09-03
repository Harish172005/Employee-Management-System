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

    console.log(
        'NEXT CLICK - BEFORE:',
        paginationState.currentPage
    );

    if (
        paginationState.currentPage >=
        paginationState.totalPages
    ) {
        return;
    }

    paginationState.currentPage++;

    console.log(
        'NEXT CLICK - AFTER:',
        paginationState.currentPage
    );

    fetchEmployees();
}

document.addEventListener('DOMContentLoaded', function () {

    document
        .getElementById('prevBtn')
        .querySelector('a')
        .addEventListener('click', previousPage);

    document
        .getElementById('nextBtn')
        .querySelector('a')
        .addEventListener('click', nextPage);

});


function updatePaginationControls() {

    const container = document.getElementById(
        'paginationContainer'
    );

    const pageText = document.getElementById(
        'pageText'
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

    pageText.textContent =
        `Page ${paginationState.currentPage} of ${paginationState.totalPages}`;

    prevBtn.classList.toggle(
        'disabled',
        paginationState.currentPage === 1
    );

    nextBtn.classList.toggle(
        'disabled',
        paginationState.currentPage === paginationState.totalPages
    );
}