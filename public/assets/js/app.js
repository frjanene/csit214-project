// public/assets/js/app.js

// 1) Open auth modal to the correct tab based on the trigger button
document.addEventListener('click', function(e){
  const trigger = e.target.closest('[data-bs-target="#authModal"][data-auth-tab]');
  if(!trigger) return;

  const tabToOpen = trigger.getAttribute('data-auth-tab'); // 'signin' | 'signup'
  const modalEl = document.getElementById('authModal');

  const showTab = () => {
    const id = tabToOpen === 'signup' ? '#signup-tab' : '#signin-tab';
    const el = document.querySelector(id);
    if (el) new bootstrap.Tab(el).show();
  };

  if (modalEl.classList.contains('show')) {
    showTab();
  } else {
    modalEl.addEventListener('shown.bs.modal', function handler(){
      modalEl.removeEventListener('shown.bs.modal', handler);
      showTab();
    });
  }
});

// 2) Password eye toggle (Font Awesome recommended for icons)
document.addEventListener('click', function(e){
  const btn = e.target.closest('.toggle-pass');
  if(!btn) return;

  const input = btn.closest('.fda-input-wrap')?.querySelector('input[data-password]');
  if(!input) return;

  const toText = input.type === 'password';
  input.type = toText ? 'text' : 'password';

  // If using Font Awesome, swap the icon class
  const i = btn.querySelector('i');
  if(i){
    i.classList.toggle('fa-eye', !toText);
    i.classList.toggle('fa-eye-slash', toText);
  }
});
