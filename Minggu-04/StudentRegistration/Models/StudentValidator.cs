using System.Text.RegularExpressions;

namespace StudentRegistration.Models
{
    /// <summary>
    /// Aturan validasi form registrasi. Dipisahkan dari tampilan agar mudah diuji dan dipakai ulang.
    /// Mengembalikan null bila valid, atau pesan kesalahan bila tidak valid.
    /// </summary>
    public static class StudentValidator
    {
        private static readonly Regex NimPattern = new(@"^\d{10}$");
        private static readonly Regex EmailPattern = new(@"^[^@\s]+@[^@\s]+\.[^@\s]+$");

        public static string? Validate(string nim, string nama, string? prodi, string? gender,
                                       DateTime? tanggalLahir, string email, string alamat)
        {
            if (string.IsNullOrWhiteSpace(nim))
                return "NIM harus diisi.";
            if (!NimPattern.IsMatch(nim.Trim()))
                return "NIM harus terdiri dari 10 digit angka.";
            if (string.IsNullOrWhiteSpace(nama))
                return "Nama harus diisi.";
            if (nama.Trim().Length < 3)
                return "Nama minimal 3 karakter.";
            if (string.IsNullOrWhiteSpace(prodi))
                return "Pilih program studi terlebih dahulu.";
            if (string.IsNullOrWhiteSpace(gender))
                return "Pilih jenis kelamin terlebih dahulu.";
            if (tanggalLahir is null)
                return "Tanggal lahir harus diisi.";
            if (tanggalLahir.Value.Date > DateTime.Today.AddYears(-15))
                return "Usia mahasiswa minimal 15 tahun.";
            if (!string.IsNullOrWhiteSpace(email) && !EmailPattern.IsMatch(email.Trim()))
                return "Format email tidak valid.";
            if (string.IsNullOrWhiteSpace(alamat))
                return "Alamat harus diisi.";
            return null;
        }
    }
}
