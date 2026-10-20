using System.Windows;
using System.Windows.Controls;
using AksesKita.Desktop.ViewModels;

namespace AksesKita.Desktop.Views;

public partial class LoginView : UserControl
{
    public LoginView()
    {
        InitializeComponent();
        DataContextChanged += OnDataContextChanged;
    }

    private LoginViewModel? ViewModel => DataContext as LoginViewModel;

    // PasswordBox sengaja tidak di-binding (demi keamanan), jadi nilainya dikirim lewat event ini.
    private void OnPasswordChanged(object sender, RoutedEventArgs e)
    {
        if (ViewModel != null) ViewModel.Password = PasswordBox.Password;
    }

    private void OnConfirmChanged(object sender, RoutedEventArgs e)
    {
        if (ViewModel != null) ViewModel.ConfirmPassword = ConfirmBox.Password;
    }

    // Saat sandi ditampilkan, pengguna mengetik di TextBox biasa; isinya disalin balik ke PasswordBox.
    private void OnPasswordPlainChanged(object sender, TextChangedEventArgs e)
    {
        if (PasswordPlain.Visibility == Visibility.Visible && PasswordBox.Password != PasswordPlain.Text)
            PasswordBox.Password = PasswordPlain.Text;
    }

    private void OnConfirmPlainChanged(object sender, TextChangedEventArgs e)
    {
        if (ConfirmPlain.Visibility == Visibility.Visible && ConfirmBox.Password != ConfirmPlain.Text)
            ConfirmBox.Password = ConfirmPlain.Text;
    }

    private void OnPasswordEyeToggled(object sender, RoutedEventArgs e)
        => Reveal(PasswordBox, PasswordPlain, PasswordEye.IsChecked == true);

    private void OnConfirmEyeToggled(object sender, RoutedEventArgs e)
        => Reveal(ConfirmBox, ConfirmPlain, ConfirmEye.IsChecked == true);

    private static void Reveal(PasswordBox hidden, TextBox plain, bool show)
    {
        if (show)
        {
            plain.Text = hidden.Password;
            plain.Visibility = Visibility.Visible;
            hidden.Visibility = Visibility.Collapsed;
            plain.Focus();
            plain.CaretIndex = plain.Text.Length;
        }
        else
        {
            var hadFocus = plain.IsKeyboardFocusWithin;
            hidden.Visibility = Visibility.Visible;
            plain.Visibility = Visibility.Collapsed;
            plain.Text = string.Empty;
            if (hadFocus) hidden.Focus();
        }
    }

    // Mengisi formulir dengan akun demo yang tertulis di kartu login.
    private void OnDemoClick(object sender, RoutedEventArgs e)
    {
        if (ViewModel == null || sender is not FrameworkElement { Tag: string kind }) return;
        ViewModel.SetModeCommand.Execute("login");
        var admin = kind == "admin";
        ViewModel.Email = admin ? "admin@akseskita.id" : "warga@akseskita.id";
        PasswordEye.IsChecked = false;
        PasswordBox.Password = admin ? "Admin2026" : "Warga2026";
        PasswordBox.Focus();
    }

    private void OnDataContextChanged(object sender, DependencyPropertyChangedEventArgs e)
    {
        if (e.OldValue is LoginViewModel old) old.PasswordsCleared -= OnPasswordsCleared;
        if (e.NewValue is LoginViewModel now) now.PasswordsCleared += OnPasswordsCleared;
    }

    // ViewModel meminta kolom kata sandi dikosongkan (setelah login, ganti mode, atau keluar).
    private void OnPasswordsCleared(object? sender, System.EventArgs e)
    {
        PasswordEye.IsChecked = false;
        ConfirmEye.IsChecked = false;
        PasswordBox.Clear();
        ConfirmBox.Clear();
    }
}
