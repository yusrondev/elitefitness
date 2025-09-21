document.addEventListener('DOMContentLoaded', () => {
    const menuLinks = document.querySelectorAll('.nav__links a');
  
    menuLinks.forEach((link) => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const targetId = link.getAttribute('href').slice(1);
  
        if (targetId) {
          const targetSection = document.getElementById(targetId);
          if (targetSection) {
            targetSection.scrollIntoView({ behavior: 'smooth' });
          } else {
            console.error(`Target section with ID "${targetId}" not found.`);
          }
        }
      });
    });
  });  