namespace StudentRegistrationSql.Models
{
    /// <summary>Satu baris data mahasiswa hasil JOIN tabel Students dan Programs.</summary>
    public class Student
    {
        public int StudentId { get; set; }
        public string Nim { get; set; } = string.Empty;
        public string FullName { get; set; } = string.Empty;
        public string Gender { get; set; } = string.Empty;
        public DateTime BirthDate { get; set; }
        public string? Email { get; set; }
        public string? Phone { get; set; }
        public string? Address { get; set; }
        public int ProgramId { get; set; }
        public string ProgramName { get; set; } = string.Empty;
        public DateTime CreatedAt { get; set; }

        public string GenderText => Gender == "P" ? "Perempuan" : "Laki-laki";
    }
}
