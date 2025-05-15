window.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success') === '1') {
        alert('Profil berhasil diperbarui.');
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});
document.getElementById('fotoInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (evt) {
            document.getElementById('previewFoto').src = evt.target.result;
        };
        reader.readAsDataURL(file);
    }
});

