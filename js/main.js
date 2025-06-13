document.addEventListener('DOMContentLoaded', () => {
    const darkModeToggle = document.getElementById('darkModeToggle');
    const body = document.body;

    // Fungsi untuk menerapkan tema
    const applyTheme = (theme) => {
        if (theme === 'dark') {
            body.classList.add('dark-mode');
        } else {
            body.classList.remove('dark-mode');
        }
    };

    // Cek tema yang tersimpan di localStorage saat halaman dimuat
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        applyTheme(savedTheme);
    } else {
        // Cek preferensi sistem operasi jika tidak ada di localStorage
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (prefersDark) {
            applyTheme('dark');
        }
    }

    // Event listener untuk tombol toggle
    darkModeToggle.addEventListener('click', () => {
        let newTheme;
        if (body.classList.contains('dark-mode')) {
            newTheme = 'light';
        } else {
            newTheme = 'dark';
        }
        applyTheme(newTheme);
        // Simpan pilihan tema ke localStorage
        localStorage.setItem('theme', newTheme);
    });
});