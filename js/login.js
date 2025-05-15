function selectRole(role) {
    document.getElementById('role').value = role;
    document.getElementById('petaniBtn').classList.remove('active');
    document.getElementById('pembeliBtn').classList.remove('active');
    if (role === 'petani') {
        document.getElementById('petaniBtn').classList.add('active');
    } else {
        document.getElementById('pembeliBtn').classList.add('active');
    }
}

document.getElementById("loginForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value.trim();
    const role = document.getElementById("role").value.trim();
    if (!username || !password || !role) {
        Swal.fire("Form Tidak Lengkap", "Harap isi semua kolom.", "warning");
        return;
    }
    fetch("controllers/login_controller.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}&role=${encodeURIComponent(role)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire("Login Berhasil!", data.message, "success").then(() => {
                window.location.href = "index.php";
            });
        } else {
            Swal.fire("Login Gagal", data.message, "error");
        }
    })
    .catch(error => {
        console.error("Login error:", error);
        Swal.fire("Server Error", "Terjadi kesalahan. Silakan coba lagi.", "error");
    });
});
