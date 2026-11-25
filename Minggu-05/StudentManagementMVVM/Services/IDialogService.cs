namespace StudentManagementMVVM.Services
{
    /// <summary>
    /// ViewModel tidak boleh memanggil MessageBox secara langsung (itu urusan View).
    /// Dialog diakses lewat interface ini agar ViewModel tetap bisa diuji tanpa UI.
    /// </summary>
    public interface IDialogService
    {
        bool Confirm(string message, string title);
        void Info(string message, string title);
    }
}
