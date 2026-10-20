using System;
using System.Threading.Tasks;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using AksesKita.Desktop.Services;

namespace AksesKita.Desktop.ViewModels;

public partial class LoginViewModel : ObservableObject
{
    private readonly MainViewModel _main;
    private readonly IAuthService _auth;

    [ObservableProperty,
     NotifyPropertyChangedFor(nameof(Heading)), NotifyPropertyChangedFor(nameof(SubmitLabel)),
     NotifyPropertyChangedFor(nameof(SwitchPrompt)), NotifyPropertyChangedFor(nameof(SwitchLabel)),
     NotifyPropertyChangedFor(nameof(IsLogin))]
    private bool _isRegister;

    [ObservableProperty] private string _name = string.Empty;
    [ObservableProperty] private string _email = string.Empty;
    [ObservableProperty] private string _password = string.Empty;
    [ObservableProperty] private string _confirmPassword = string.Empty;
    [ObservableProperty] private string _errorMessage = string.Empty;
    [ObservableProperty] private bool _isBusy;

    /// <summary>PasswordBox tidak bisa di-binding, jadi tampilan perlu diberi tahu saat isinya harus dikosongkan.</summary>
    public event EventHandler? PasswordsCleared;

    public bool IsLogin => !IsRegister;
    public string Heading => IsRegister ? "Buat akun baru warga Surabaya" : "Masuk ke portal layanan aksesibilitas warga";
    public string SubmitLabel => IsRegister ? "Daftar Akun AksesKita" : "Masuk";
    public string SwitchPrompt => IsRegister ? "Sudah punya akun?" : "Belum punya akun?";
    public string SwitchLabel => IsRegister ? "Masuk" : "Daftar sekarang";

    public LoginViewModel(MainViewModel main, IAuthService auth)
    {
        _main = main;
        _auth = auth;
    }

    /// <summary>Mengosongkan semua isian (dipanggil saat keluar).</summary>
    public void Clear()
    {
        Name = Email = Password = ConfirmPassword = ErrorMessage = string.Empty;
        IsRegister = false;
        PasswordsCleared?.Invoke(this, EventArgs.Empty);
    }

    /// <summary>Dipakai tab "Masuk" / "Daftar Akun Baru": hanya berganti bila mode yang dipilih berbeda.</summary>
    [RelayCommand]
    private void SetMode(string mode)
    {
        if ((mode == "register") != IsRegister) ToggleMode();
    }

    [RelayCommand]
    private void ToggleMode()
    {
        IsRegister = !IsRegister;
        ErrorMessage = string.Empty;
        Password = ConfirmPassword = string.Empty;
        PasswordsCleared?.Invoke(this, EventArgs.Empty);
    }

    [RelayCommand]
    private async Task Submit()
    {
        if (IsBusy) return;
        IsBusy = true;
        ErrorMessage = string.Empty;
        try
        {
            var result = IsRegister
                ? await _auth.RegisterAsync(Name, Email, Password, ConfirmPassword)
                : await _auth.LoginAsync(Email, Password);

            if (!result.Success || result.User == null)
            {
                ErrorMessage = "⚠ " + (result.Error ?? "Terjadi kesalahan.");
                return;
            }

            var user = result.User;
            Clear();
            _main.LoginUser(user);
        }
        finally
        {
            IsBusy = false;
        }
    }
}
