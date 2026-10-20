using System.Windows;
using System.Windows.Media;

namespace AksesKita.Desktop;

/// <summary>
/// Properti tambahan untuk kontrol bawaan WPF, dibaca oleh template di Themes/Styles.xaml.
/// Icon: ikon garis di sisi kiri tombol, menu, atau kolom isian.
/// Placeholder: teks petunjuk abu-abu yang tampil selama kolom isian masih kosong.
/// </summary>
public static class Ui
{
    public static readonly DependencyProperty IconProperty = DependencyProperty.RegisterAttached(
        "Icon", typeof(Geometry), typeof(Ui), new FrameworkPropertyMetadata(null));

    public static Geometry? GetIcon(DependencyObject element) => (Geometry?)element.GetValue(IconProperty);
    public static void SetIcon(DependencyObject element, Geometry? value) => element.SetValue(IconProperty, value);

    public static readonly DependencyProperty PlaceholderProperty = DependencyProperty.RegisterAttached(
        "Placeholder", typeof(string), typeof(Ui), new FrameworkPropertyMetadata(string.Empty));

    public static string GetPlaceholder(DependencyObject element) => (string)element.GetValue(PlaceholderProperty);
    public static void SetPlaceholder(DependencyObject element, string value) => element.SetValue(PlaceholderProperty, value);
}
