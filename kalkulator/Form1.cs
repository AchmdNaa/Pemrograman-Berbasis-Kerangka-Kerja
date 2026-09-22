using System;
using System.Windows.Forms;

namespace Kalkulator
{
    public partial class Form1 : Form
    {
        private double totalHasil = 0;
        private string operasiPending = "";
        private bool sedangKetikAngkaBaru = true;
        private bool selesaiHitung = false;

        public Form1()
        {
            InitializeComponent();
        }

        private void FormatDisplay(double val)
        {
            lblDisplay.Text = val.ToString("#,##0.######");
        }

        private void BtnAngka_Click(object sender, EventArgs e)
        {
            Button btn = (Button)sender;
            string input = btn.Text;

            if (selesaiHitung)
            {
                lblHistory.Text = "";
                lblDisplay.Text = input;
                selesaiHitung = false;
                sedangKetikAngkaBaru = false;
                return;
            }

            if (sedangKetikAngkaBaru || lblDisplay.Text == "0")
            {
                lblDisplay.Text = input;
                sedangKetikAngkaBaru = false;
            }
            else
            {
                string raw = lblDisplay.Text.Replace(",", "");
                if (raw.Length < 9)
                {
                    lblDisplay.Text = raw + input;
                    if (double.TryParse(lblDisplay.Text, out double val))
                    {
                        FormatDisplay(val);
                    }
                }
            }
        }

        private void BtnTitik_Click(object sender, EventArgs e)
        {
            if (selesaiHitung)
            {
                lblHistory.Text = "";
                lblDisplay.Text = "0.";
                selesaiHitung = false;
                sedangKetikAngkaBaru = false;
                return;
            }

            if (sedangKetikAngkaBaru)
            {
                lblDisplay.Text = "0.";
                sedangKetikAngkaBaru = false;
            }
            else if (!lblDisplay.Text.Contains("."))
            {
                lblDisplay.Text += ".";
            }
        }

        private void BtnOperator_Click(object sender, EventArgs e)
        {
            Button btn = (Button)sender;
            string op = btn.Text;
            double angkaSekarang = double.TryParse(lblDisplay.Text.Replace(",", ""), out double val) ? val : 0;

            if (selesaiHitung)
            {
                lblHistory.Text = $"{totalHasil:#,##0.######} {op} ";
                operasiPending = op;
                selesaiHitung = false;
                sedangKetikAngkaBaru = true;
                return;
            }

            if (!sedangKetikAngkaBaru && !string.IsNullOrEmpty(operasiPending))
            {
                HitungHasil(angkaSekarang);
                FormatDisplay(totalHasil);
                lblHistory.Text += $"{angkaSekarang:#,##0.######} {op} ";
            }
            else
            {
                totalHasil = angkaSekarang;
                lblHistory.Text = $"{totalHasil:#,##0.######} {op} ";
            }

            operasiPending = op;
            sedangKetikAngkaBaru = true;
        }

        private void BtnSamaDengan_Click(object sender, EventArgs e)
        {
            if (string.IsNullOrEmpty(operasiPending) || selesaiHitung) return;

            double angkaKedua = double.TryParse(lblDisplay.Text.Replace(",", ""), out double val) ? val : 0;
            lblHistory.Text += $"{angkaKedua:#,##0.######} =";

            HitungHasil(angkaKedua);
            FormatDisplay(totalHasil);

            operasiPending = "";
            sedangKetikAngkaBaru = true;
            selesaiHitung = true;
        }

        private void HitungHasil(double angka)
        {
            switch (operasiPending)
            {
                case "+": totalHasil += angka; break;
                case "-": totalHasil -= angka; break;
                case "×": totalHasil *= angka; break;
                case "÷":
                    if (angka == 0)
                    {
                        MessageBox.Show("Tidak dapat membagi dengan nol!", "Peringatan", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                        totalHasil = 0;
                    }
                    else
                    {
                        totalHasil /= angka;
                    }
                    break;
            }
        }

        private void BtnNegatif_Click(object sender, EventArgs e)
        {
            if (double.TryParse(lblDisplay.Text.Replace(",", ""), out double val))
            {
                val = -val;
                FormatDisplay(val);
            }
        }

        private void BtnPersen_Click(object sender, EventArgs e)
        {
            if (double.TryParse(lblDisplay.Text.Replace(",", ""), out double val))
            {
                val = val / 100.0;
                FormatDisplay(val);
                sedangKetikAngkaBaru = true;
            }
        }

        private void BtnClear_Click(object sender, EventArgs e)
        {
            lblDisplay.Text = "0";
            lblHistory.Text = "";
            totalHasil = 0;
            operasiPending = "";
            sedangKetikAngkaBaru = true;
            selesaiHitung = false;
        }
    }
}