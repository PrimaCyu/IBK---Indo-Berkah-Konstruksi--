const images = ["/image/ElementProgram/rumah2.jpg", "/image/ElementProgram/rumah9.jpg"];

const hero = document.getElementById("hero");

if (hero) {
    let current = 0;

    hero.style.backgroundImage = `url(${images[0]})`;

    setInterval(() => {
        current = (current + 1) % images.length;
        hero.style.backgroundImage = `url(${images[current]})`;
    }, 5000);
}