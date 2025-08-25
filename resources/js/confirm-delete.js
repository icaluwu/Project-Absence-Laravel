document.addEventListener('submit', function (e) {
  const form = e.target;
  if (form && form.matches('form[data-confirm]')) {
    const message = form.getAttribute('data-confirm') || 'Apakah sudah ada keputusan?';
    if (!window.confirm(message)) {
      e.preventDefault();
    }
  }
});
