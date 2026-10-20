using System.Windows;
using AksesKita.Desktop.Data;

namespace AksesKita.Desktop;

public partial class App : Application
{
    protected override void OnStartup(StartupEventArgs e)
    {
        // Buat database dan kategori awal sebelum jendela utama dibuka
        using (var db = new AksesKitaDbContext())
        {
            db.Database.EnsureCreated();
        }

        base.OnStartup(e);
    }
}
