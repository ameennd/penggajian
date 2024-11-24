// Handle Absensi Submission
document
  .getElementById("form-absensi")
  .addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent default form submission

    const nrk = document.getElementById("no_karyawan").value;
    const statusAbsensi = document.getElementById("status_absensi").value;

    fetch("proses_absensi.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `no_karyawan=${nrk}&status_absensi=${statusAbsensi}`,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          const nama = data.data.nama;
          const noKaryawan = data.data.no_karyawan;
          showPopup(`Terimakasih! Selamat Bekerja ${nama} (${noKaryawan})`);
        } else {
          showPopup(data.message || "Gagal menyimpan absensi.");
        }
      })
      .catch(() => {
        showPopup("Terjadi kesalahan. Silakan coba lagi.");
      });
  });

// Function to Show Popup
function showPopup(message) {
  const popup = document.getElementById("popup");
  const popupMessage = document.getElementById("popup-message");
  popupMessage.textContent = message;
  popup.classList.remove("hidden");
  popup.style.display = "block";
}

// Function to Close Popup
function closePopup() {
  const popup = document.getElementById("popup");
  popup.classList.add("hidden");
  popup.style.display = "none";
}
