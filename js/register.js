document.getElementById("registerForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const username = document.getElementById("usernameRegister").value.trim();
    const password = document.getElementById("passwordRegister").value.trim();
    const alamat = document.getElementById("alamatRegister").value.trim();
    const no_telp = document.getElementById("noTelpRegister").value.trim();
    const role = document.getElementById("roleRegister").value.trim();

    if (!username || !password || !alamat || !no_telp || !role) {
        Swal.fire("Form Tidak Lengkap", "Harap isi semua kolom.", "warning");
        return;
    }
    fetch("controllers/register_controller.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}&alamat=${encodeURIComponent(alamat)}&no_telp=${encodeURIComponent(no_telp)}&role=${encodeURIComponent(role)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire("Register Berhasil!", data.message, "success").then(() => {
                window.location.href = "login.php";
            });
        } else {
            Swal.fire("Register Gagal", data.message, "error");
        }
    })
    .catch(error => {
        console.error("Register error:", error);
        Swal.fire("Server Error", "Terjadi kesalahan. Silakan coba lagi.", "error");
    });
});
