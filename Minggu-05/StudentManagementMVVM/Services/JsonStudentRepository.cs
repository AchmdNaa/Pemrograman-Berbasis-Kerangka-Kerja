using System.IO;
using System.Text.Json;
using StudentManagementMVVM.Models;

namespace StudentManagementMVVM.Services
{
    /// <summary>
    /// Menyimpan data mahasiswa ke file JSON di folder AppData,
    /// sehingga data tetap ada ketika aplikasi ditutup lalu dibuka kembali.
    /// </summary>
    public class JsonStudentRepository : IStudentRepository
    {
        private static readonly JsonSerializerOptions Options = new() { WriteIndented = true };
        private readonly string _filePath;

        public JsonStudentRepository(string? filePath = null)
        {
            _filePath = filePath ?? Path.Combine(
                Environment.GetFolderPath(Environment.SpecialFolder.ApplicationData),
                "StudentManagementMVVM", "students.json");
        }

        public IReadOnlyList<Student> GetAll()
        {
            if (!File.Exists(_filePath)) return SeedData();
            try
            {
                string json = File.ReadAllText(_filePath);
                return JsonSerializer.Deserialize<List<Student>>(json, Options) ?? new List<Student>();
            }
            catch (JsonException)
            {
                // File rusak: mulai lagi dari data contoh daripada aplikasi gagal dibuka.
                return SeedData();
            }
        }

        public void SaveAll(IEnumerable<Student> students)
        {
            Directory.CreateDirectory(Path.GetDirectoryName(_filePath)!);
            File.WriteAllText(_filePath, JsonSerializer.Serialize(students, Options));
        }

        private static List<Student> SeedData() => new()
        {
            new() { Nim = "5025231265", Nama = "Achmad Najwa", ProgramStudi = "Teknik Informatika", Angkatan = 2023, Ipk = 3.62, Email = "najwa@student.its.ac.id" },
            new() { Nim = "5025231288", Nama = "Leyan Harits", ProgramStudi = "Teknik Informatika", Angkatan = 2023, Ipk = 3.48, Email = "leyan@student.its.ac.id" },
            new() { Nim = "5026231010", Nama = "Siti Rahmawati", ProgramStudi = "Sistem Informasi", Angkatan = 2023, Ipk = 3.75, Email = "siti@student.its.ac.id" },
            new() { Nim = "5027241020", Nama = "Budi Santoso", ProgramStudi = "Teknologi Informasi", Angkatan = 2024, Ipk = 2.95, Email = "budi@student.its.ac.id" },
            new() { Nim = "5025241175", Nama = "Derryell Josua", ProgramStudi = "Teknik Informatika", Angkatan = 2024, Ipk = 3.21, Email = "derryell@student.its.ac.id" },
        };
    }
}
