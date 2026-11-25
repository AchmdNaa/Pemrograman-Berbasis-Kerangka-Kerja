using System.Windows;
using StudentManagementMVVM.Services;
using StudentManagementMVVM.ViewModels;
using StudentManagementMVVM.Views;

namespace StudentManagementMVVM
{
    public partial class App : Application
    {
        /// <summary>
        /// Composition root: semua dependensi dirakit di sini lalu ViewModel
        /// dipasang sebagai DataContext jendela utama.
        /// </summary>
        protected override void OnStartup(StartupEventArgs e)
        {
            base.OnStartup(e);

            var viewModel = new MainViewModel(new JsonStudentRepository(), new MessageBoxDialogService());
            var window = new MainWindow { DataContext = viewModel };
            window.Show();
        }
    }
}
