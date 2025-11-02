document.addEventListener("DOMContentLoaded", () => {
  const track = document.querySelector(".carousel-track");
  const cards = Array.from(document.querySelectorAll(".product-card"));

  if (!track || !cards.length) return;

  // ✅ Duplicate all cards to create a seamless loop
  track.innerHTML += track.innerHTML;

  // Set animation speed (pixels per frame)
  let speed = 0.5;
  let position = 0;

  function animate() {
    position -= speed;
    track.style.transform = `translateX(${position}px)`;

    // Reset position to halfway (because we doubled the cards)
    if (Math.abs(position) >= track.scrollWidth / 2) {
      position = 0;
    }

    requestAnimationFrame(animate);
  }

  animate();
});
