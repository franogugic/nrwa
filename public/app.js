function showMessage(button, message, type) {
    var container = button.closest('.movie-card') || button.closest('.detail-actions') || button.parentElement;
    var existing = container.querySelector('.inline-message');

    if (!existing) {
        existing = document.createElement('p');
        existing.className = 'inline-message';
        container.appendChild(existing);
    }

    existing.textContent = message;
    existing.dataset.type = type;
}

function addMovieToList(button) {
    var movieId = Number(button.dataset.movieId);
    var status = button.dataset.status;
    var originalText = button.textContent.trim();

    button.disabled = true;
    button.textContent = 'Dodavanje...';

    fetch('/api/user-movies', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            film_id: movieId,
            status: status
        })
    })
        .then(function (response) {
            return response.json().then(function (payload) {
                return {
                    ok: response.ok,
                    status: response.status,
                    payload: payload
                };
            });
        })
        .then(function (result) {
            if (!result.ok) {
                showMessage(button, result.payload.error || 'Dodavanje nije uspjelo.', 'error');
                return;
            }

            showMessage(button, result.payload.message || 'Film je dodan.', 'success');
        })
        .catch(function () {
            showMessage(button, 'API trenutno nije dostupan.', 'error');
        })
        .finally(function () {
            button.disabled = false;
            button.textContent = originalText;
        });
}

document.addEventListener('click', function (event) {
    var button = event.target.closest('[data-add-movie]');

    if (!button) {
        return;
    }

    addMovieToList(button);
});
