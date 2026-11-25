namespace StudentRegistrationSql.Models
{
    /// <summary>Program studi (tabel dbo.Programs).</summary>
    public class StudyProgram
    {
        public int ProgramId { get; set; }
        public string ProgramCode { get; set; } = string.Empty;
        public string ProgramName { get; set; } = string.Empty;

        public override string ToString() => ProgramName;
    }
}
