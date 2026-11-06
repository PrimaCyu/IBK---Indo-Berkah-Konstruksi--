const island = document.getElementById("dynamicIsland");
const searchBtn = document.getElementById("searchBtn");
const searchInput = document.getElementById("searchInput");
const menu = document.getElementById("menu");

let expanded = false;

searchBtn.addEventListener("click", () => {
    expanded = !expanded;

    if (expanded) {
        island.classList.remove("w-[600px]");
        island.classList.add("w-[800px]");

        searchInput.classList.remove("w-0", "opacity-0");
        searchInput.classList.add("w-40", "opacity-100");

        menu.classList.add("opacity-0");
    } else {
        island.classList.remove("w-[800px]");
        island.classList.add("w-[600px]");

        searchInput.classList.remove("w-40", "opacity-100");
        searchInput.classList.add("w-0", "opacity-0");

        menu.classList.remove("opacity-0");
    }
});
