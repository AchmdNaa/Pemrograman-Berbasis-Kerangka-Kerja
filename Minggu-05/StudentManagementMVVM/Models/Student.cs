namespace StudentManagementMVVM.Models
{
    /// <summary>Model: hanya berisi data, tanpa logika tampilan.</summary>
    public class Student
    {
        public string Nim { get; set; } = string.Empty;
        public string Nama { get; set; } = string.Empty;
        public string ProgramStudi { get; set; } = string.Empty;
        public int Angkatan { get; set; }
        public double Ipk { get; set; }
        public string Email { get; set; } = string.Empty;

        public string Predikat => Ipk switch
        {
            >= 3.51 => "Cumlaude",
            >= 3.01 => "Sangat Memuaskan",
            >= 2.76 => "Memuaskan",
            _ => "Cukup"
        };

        public Student Clone() => (Student)MemberwiseClone();
    }
}
