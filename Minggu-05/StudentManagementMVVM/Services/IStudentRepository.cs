using StudentManagementMVVM.Models;

namespace StudentManagementMVVM.Services
{
    /// <summary>Abstraksi penyimpanan data (Repository Pattern).</summary>
    public interface IStudentRepository
    {
        IReadOnlyList<Student> GetAll();
        void SaveAll(IEnumerable<Student> students);
    }
}
