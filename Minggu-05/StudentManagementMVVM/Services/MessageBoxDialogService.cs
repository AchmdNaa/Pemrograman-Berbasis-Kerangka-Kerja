using System.Windows;

namespace StudentManagementMVVM.Services
{
    /// <summary>Implementasi IDialogService memakai MessageBox WPF (bagian dari lapisan View).</summary>
    public class MessageBoxDialogService : IDialogService
    {
        public bool Confirm(string message, string title) =>
            MessageBox.Show(message, title, MessageBoxButton.YesNo, MessageBoxImage.Question) == MessageBoxResult.Yes;

        public void Info(string message, string title) =>
            MessageBox.Show(message, title, MessageBoxButton.OK, MessageBoxImage.Information);
    }
}
