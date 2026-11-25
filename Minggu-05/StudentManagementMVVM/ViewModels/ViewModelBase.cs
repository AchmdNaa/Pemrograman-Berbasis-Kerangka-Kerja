using System.ComponentModel;
using System.Runtime.CompilerServices;

namespace StudentManagementMVVM.ViewModels
{
    /// <summary>
    /// Kelas dasar ViewModel. INotifyPropertyChanged memberi tahu View (lewat data binding)
    /// setiap kali nilai properti berubah, sehingga tampilan ter-update otomatis.
    /// </summary>
    public abstract class ViewModelBase : INotifyPropertyChanged
    {
        public event PropertyChangedEventHandler? PropertyChanged;

        protected void OnPropertyChanged([CallerMemberName] string? propertyName = null)
            => PropertyChanged?.Invoke(this, new PropertyChangedEventArgs(propertyName));

        protected bool SetProperty<T>(ref T field, T value, [CallerMemberName] string? propertyName = null)
        {
            if (EqualityComparer<T>.Default.Equals(field, value)) return false;
            field = value;
            OnPropertyChanged(propertyName);
            return true;
        }
    }
}
