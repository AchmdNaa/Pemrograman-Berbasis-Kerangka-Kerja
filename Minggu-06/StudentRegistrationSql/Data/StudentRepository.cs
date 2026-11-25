using Microsoft.Data.SqlClient;
using StudentRegistrationSql.Models;

namespace StudentRegistrationSql.Data
{
    /// <summary>
    /// Semua akses database lewat ADO.NET dikumpulkan di kelas ini:
    /// SqlConnection (koneksi) → SqlCommand (query + parameter) → SqlDataReader (membaca hasil).
    /// Setiap input pengguna dikirim sebagai parameter (@nama) untuk mencegah SQL Injection.
    /// </summary>
    public class StudentRepository
    {
        private readonly string _connectionString;

        public StudentRepository(string? connectionString = null)
        {
            _connectionString = connectionString ?? DbConfig.ConnectionString;
        }

        private const string SelectStudents = @"
            SELECT s.StudentId, s.Nim, s.FullName, s.Gender, s.BirthDate, s.Email, s.Phone,
                   s.Address, s.ProgramId, p.ProgramName, s.CreatedAt
            FROM dbo.Students AS s
            INNER JOIN dbo.Programs AS p ON p.ProgramId = s.ProgramId";

        /// <summary>Mengambil seluruh mahasiswa (JOIN dengan program studi).</summary>
        public List<Student> GetAll() => Search(keyword: null, programId: null);

        /// <summary>Mencari mahasiswa berdasarkan NIM/nama dan (opsional) program studi.</summary>
        public List<Student> Search(string? keyword, int? programId)
        {
            string sql = SelectStudents + @"
            WHERE (@keyword IS NULL OR s.Nim LIKE @pattern OR s.FullName LIKE @pattern)
              AND (@programId IS NULL OR s.ProgramId = @programId)
            ORDER BY s.Nim;";

            using var connection = new SqlConnection(_connectionString);
            using var command = new SqlCommand(sql, connection);

            string? kw = string.IsNullOrWhiteSpace(keyword) ? null : keyword.Trim();
            command.Parameters.Add("@keyword", System.Data.SqlDbType.NVarChar, 100).Value = (object?)kw ?? DBNull.Value;
            command.Parameters.Add("@pattern", System.Data.SqlDbType.NVarChar, 102).Value = kw is null ? DBNull.Value : $"%{kw}%";
            command.Parameters.Add("@programId", System.Data.SqlDbType.Int).Value = (object?)programId ?? DBNull.Value;

            connection.Open();
            using SqlDataReader reader = command.ExecuteReader();

            var students = new List<Student>();
            while (reader.Read())
            {
                students.Add(new Student
                {
                    StudentId   = reader.GetInt32(reader.GetOrdinal("StudentId")),
                    Nim         = reader.GetString(reader.GetOrdinal("Nim")),
                    FullName    = reader.GetString(reader.GetOrdinal("FullName")),
                    Gender      = reader.GetString(reader.GetOrdinal("Gender")),
                    BirthDate   = reader.GetDateTime(reader.GetOrdinal("BirthDate")),
                    // Kolom nullable: cek DBNull sebelum membaca
                    Email       = GetNullableString(reader, "Email"),
                    Phone       = GetNullableString(reader, "Phone"),
                    Address     = GetNullableString(reader, "Address"),
                    ProgramId   = reader.GetInt32(reader.GetOrdinal("ProgramId")),
                    ProgramName = reader.GetString(reader.GetOrdinal("ProgramName")),
                    CreatedAt   = reader.GetDateTime(reader.GetOrdinal("CreatedAt"))
                });
            }
            return students;
        }

        /// <summary>Mini challenge: total mahasiswa dengan SELECT COUNT(*).</summary>
        public int GetCount()
        {
            using var connection = new SqlConnection(_connectionString);
            using var command = new SqlCommand("SELECT COUNT(*) FROM dbo.Students;", connection);
            connection.Open();
            return Convert.ToInt32(command.ExecuteScalar());
        }

        public List<StudyProgram> GetPrograms()
        {
            using var connection = new SqlConnection(_connectionString);
            using var command = new SqlCommand(
                "SELECT ProgramId, ProgramCode, ProgramName FROM dbo.Programs ORDER BY ProgramName;", connection);
            connection.Open();
            using var reader = command.ExecuteReader();

            var programs = new List<StudyProgram>();
            while (reader.Read())
            {
                programs.Add(new StudyProgram
                {
                    ProgramId = reader.GetInt32(0),
                    ProgramCode = reader.GetString(1),
                    ProgramName = reader.GetString(2)
                });
            }
            return programs;
        }

        public bool NimExists(string nim)
        {
            using var connection = new SqlConnection(_connectionString);
            using var command = new SqlCommand("SELECT COUNT(*) FROM dbo.Students WHERE Nim = @nim;", connection);
            command.Parameters.Add("@nim", System.Data.SqlDbType.Char, 10).Value = nim;
            connection.Open();
            return Convert.ToInt32(command.ExecuteScalar()) > 0;
        }

        /// <summary>Menyimpan mahasiswa baru, mengembalikan StudentId yang dibuat SQL Server.</summary>
        public int Insert(Student s)
        {
            const string sql = @"
                INSERT INTO dbo.Students (Nim, FullName, Gender, BirthDate, Email, Phone, Address, ProgramId)
                OUTPUT INSERTED.StudentId
                VALUES (@nim, @name, @gender, @birthDate, @email, @phone, @address, @programId);";

            using var connection = new SqlConnection(_connectionString);
            using var command = new SqlCommand(sql, connection);
            command.Parameters.Add("@nim", System.Data.SqlDbType.Char, 10).Value = s.Nim;
            command.Parameters.Add("@name", System.Data.SqlDbType.NVarChar, 100).Value = s.FullName;
            command.Parameters.Add("@gender", System.Data.SqlDbType.Char, 1).Value = s.Gender;
            command.Parameters.Add("@birthDate", System.Data.SqlDbType.Date).Value = s.BirthDate.Date;
            command.Parameters.Add("@email", System.Data.SqlDbType.NVarChar, 100).Value = ToDbValue(s.Email);
            command.Parameters.Add("@phone", System.Data.SqlDbType.NVarChar, 20).Value = ToDbValue(s.Phone);
            command.Parameters.Add("@address", System.Data.SqlDbType.NVarChar, 255).Value = ToDbValue(s.Address);
            command.Parameters.Add("@programId", System.Data.SqlDbType.Int).Value = s.ProgramId;

            connection.Open();
            return Convert.ToInt32(command.ExecuteScalar());
        }

        public bool Delete(int studentId)
        {
            using var connection = new SqlConnection(_connectionString);
            using var command = new SqlCommand("DELETE FROM dbo.Students WHERE StudentId = @id;", connection);
            command.Parameters.Add("@id", System.Data.SqlDbType.Int).Value = studentId;
            connection.Open();
            return command.ExecuteNonQuery() == 1;
        }

        private static string? GetNullableString(SqlDataReader reader, string column)
        {
            int ordinal = reader.GetOrdinal(column);
            return reader.IsDBNull(ordinal) ? null : reader.GetString(ordinal);
        }

        private static object ToDbValue(string? value) =>
            string.IsNullOrWhiteSpace(value) ? DBNull.Value : value.Trim();
    }
}
