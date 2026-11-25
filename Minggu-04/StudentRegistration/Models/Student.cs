namespace StudentRegistration.Models
{
    /// <summary>
    /// Model data mahasiswa yang didaftarkan melalui form.
    /// </summary>
    public class Student
    {
        public string Nim { get; set; } = string.Empty;
        public string Nama { get; set; } = string.Empty;
        public string ProgramStudi { get; set; } = string.Empty;
        public string JenisKelamin { get; set; } = string.Empty;
        public DateTime TanggalLahir { get; set; }
        public string Email { get; set; } = string.Empty;
        public string Alamat { get; set; } = string.Empty;

        public int Usia
        {
            get
            {
                var today = DateTime.Today;
                int usia = today.Year - TanggalLahir.Year;
                if (TanggalLahir.Date > today.AddYears(-usia)) usia--;
                return usia;
            }
        }

        /// <summary>Teks yang ditampilkan pada ListBox.</summary>
        public string Ringkasan => $"{Nim}  |  {Nama}  |  {ProgramStudi}  |  {JenisKelamin}";
    }
}
